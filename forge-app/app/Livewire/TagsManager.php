<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tag;
use Cocur\Slugify\Slugify;

class TagsManager extends Component
{
    public $tags = [];
    public $newTag = '';
    public $newTagColor = '';
    public $newTagIcon = '';
    public $newTagDisplayOnlyOnItemCards = false;

    public function mount($existingTags = [])
    {
        $this->tags = $existingTags;
    }

    public function addTag()
    {
        if (!empty($this->newTag)) {
            // Slugify the tag name
            $slugify = new Slugify();

            $sluggedTag = $slugify->slugify($this->newTag);

            $tag = Tag::firstOrCreate(
                ['slug' => $sluggedTag],
                ['name' => $this->newTag],
                ['color' => $this->newTagColor ?? '#c3c3c3'],
                ['icon' => $this->newTagIcon ?? null],
                ['display_only_on_item_cards' => $this->newTagDisplayOnlyOnItemCards ?? false]
            );

            if (!in_array($tag->id, $this->tags)) {
                $this->tags[] = $tag->id;
            }

            $this->newTag = '';
        }
    }

    public function removeTag($tagId)
    {
        $this->tags = array_filter($this->tags, fn ($id) => $id != $tagId);
    }

    public function render()
    {
        return view('livewire.tags-manager', [
            'availableTags' => Tag::whereIn('id', $this->tags)->get(),
        ]);
    }
}
