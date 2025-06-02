<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * @OA\Info(title="Translation Management API", version="1.0.0")
 */
class TranslationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/translations",
     *     summary="List translations with optional filters",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="tag", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="key", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="content", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of translations")
     * )
     */
    public function index(Request $request)
    {
        $query = Translation::query();

        if ($request->has('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('slug', $request->tag));
        }

        if ($request->has('key')) {
            $query->where('key', 'like', "%{$request->key}%");
        }

        if ($request->has('content')) {
            $query->where('content->en', 'like', "%{$request->content}%");
        }

        return response()->json($query->with('tags')->paginate(50));
    }

    /**
     * @OA\Post(
     *     path="/api/translations",
     *     summary="Create a new translation",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"key", "content"},
     *             @OA\Property(property="key", type="string"),
     *             @OA\Property(property="content", type="object"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Translation created")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|unique:translations',
            'content' => 'required|array',
            'tags' => 'array'
        ]);

        $translation = Translation::create($data);

        if (!empty($data['tags'])) {
            $tagIds = [];
            foreach ($data['tags'] as $tagSlug) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagSlug)],
                    ['name' => ucfirst($tagSlug)]
                );
                $tagIds[] = $tag->id;
            }
            $translation->tags()->sync($tagIds);
        }

        foreach (array_keys($data['content']) as $locale) {
            Cache::forget("export-{$locale}");
        }

        return response()->json($translation->load('tags'));
    }

    /**
     * @OA\Put(
     *     path="/api/translations/{id}",
     *     summary="Update an existing translation",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="content", type="object"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Translation updated")
     * )
     */
    public function update(Request $request, Translation $translation)
    {
        $data = $request->validate([
            'content' => 'sometimes|array',
            'tags' => 'array'
        ]);

        $translation->update($data);

        if (isset($data['tags'])) {
            $tagIds = [];
            foreach ($data['tags'] as $tagSlug) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagSlug)],
                    ['name' => ucfirst($tagSlug)]
                );
                $tagIds[] = $tag->id;
            }
            $translation->tags()->sync($tagIds);
        }

        if (isset($data['content'])) {
            foreach (array_keys($data['content']) as $locale) {
                Cache::forget("export-{$locale}");
            }
        }

        return response()->json($translation->load('tags'));
    }

    /**
     * @OA\Get(
     *     path="/api/translations/{id}",
     *     summary="Get a specific translation",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Translation object")
     * )
     */
    public function show(Translation $translation)
    {
        return response()->json($translation->load('tags'));
    }

    /**
     * @OA\Delete(
     *     path="/api/translations/{id}",
     *     summary="Delete a translation",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted message")
     * )
     */
    public function destroy(Translation $translation)
    {
        foreach (array_keys($translation->content) as $locale) {
            Cache::forget("export-{$locale}");
        }

        $translation->delete();
        return response()->json(['message' => 'Deleted']);
    }

    /**
     * @OA\Get(
     *     path="/api/translations/export",
     *     summary="Export translations by locale",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="locale", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Exported translations as key-value JSON")
     * )
     */
    public function export(Request $request)
    {
        $locale = $request->get('locale', 'en');

        $translations = Cache::remember("export-{$locale}", 60, function () use ($locale) {
            return Translation::all()->mapWithKeys(function ($item) use ($locale) {
                return [$item->key => $item->content[$locale] ?? null];
            });
        });

        return response()->json($translations);
    }
}
