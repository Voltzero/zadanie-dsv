<?php

namespace App\Models\Enums;

enum ItemEnum: string
{
    case BOOK = 'book';
    case COMIC = 'comic';
    case SHORT_STORY_COLLECTION = 'short_story_collection';
}
