<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\AuthorCollection;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthorController extends Controller
{
    public function getAuthors(): AuthorCollection
    {
        return new AuthorCollection(Author::all());
    }
}
