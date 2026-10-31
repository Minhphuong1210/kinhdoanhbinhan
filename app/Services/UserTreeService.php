<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserTreeService
{
    public function deleteAndRebuildTree(User $user): void
    {
        $parentId = $user->parent_id;

        $children = User::where('parent_id', $user->id)
            ->orderBy('id')
            ->get();

        // không có con
        if ($children->isEmpty()) {

            DB::table('admin_hierarchy')
                ->where('ancestor_id', $user->id)
                ->orWhere('descendant_id', $user->id)
                ->delete();

            $user->delete();

            return;
        }

        /**
         * con đầu lên thay
         */
        $replaceUser = $children->shift();

        $replaceUser->update([
            'parent_id' => $parentId,
        ]);

        /**
         * các con còn lại
         * chuyển sang dưới replaceUser
         */
        foreach ($children as $child) {

            $newParentId = app(
                OrderService::class
            )->timCha($replaceUser->id);

            if (!$newParentId) {
                $newParentId = $replaceUser->id;
            }

            $child->update([
                'parent_id' => $newParentId,
            ]);
        }

        /**
         * xoá hierarchy node cũ
         */
        DB::table('admin_hierarchy')
            ->where('ancestor_id', $user->id)
            ->orWhere('descendant_id', $user->id)
            ->delete();

        /**
         * xoá user
         */
        $user->delete();

        /**
         * rebuild toàn bộ subtree
         */
        $this->rebuildSubTree($replaceUser);
    }
    /**
     * rebuild cây closure
     */
    private function rebuildHierarchy(User $user): void
    {
        DB::table('admin_hierarchy')
            ->where('descendant_id', $user->id)
            ->delete();

        DB::table('admin_hierarchy')
            ->insert([
                'ancestor_id' => $user->id,
                'descendant_id' => $user->id,
                'depth' => 0,
            ]);

        $depth = 1;

        $current = $user->parent_id;

        while ($current && $depth <= 6) {

            DB::table('admin_hierarchy')
                ->updateOrInsert(
                    [
                        'ancestor_id' => $current,
                        'descendant_id' => $user->id,
                    ],
                    [
                        'depth' => $depth,
                    ]
                );

            $parent = User::find($current);

            if (!$parent) {
                break;
            }

            $current = $parent->parent_id;

            $depth++;
        }
    }

    private function rebuildSubTree(User $root): void
    {
        $this->rebuildHierarchy($root);

        $children = User::where(
            'parent_id',
            $root->id
        )->get();

        foreach ($children as $child) {
            $this->rebuildSubTree($child);
        }
    }

}