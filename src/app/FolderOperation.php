<?php

namespace App;

enum FolderOperation: string
{
    case SAVE = 'save';
    case PROPAGATE = 'propagate';
    case MERGE = 'merge';
}
