<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Notifications\DiscordTestNotification;

class WorkspaceDiscordSettingsTest extends Controller
{
    public function __invoke($workspace)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $workspace->notify(new DiscordTestNotification);
    }
}
