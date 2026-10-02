<?php

namespace App\Http\Resources\Settings;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Jenssegers\Agent\Agent;

/**
 * @property string $id
 * @property string|null $user_agent
 * @property string|null $ip_address
 * @property int $last_activity
 */
class SessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $agent = tap(new Agent, fn (Agent $agent) => $agent->setUserAgent($this->user_agent));

        return [
            'id' => $this->id,
            'agent' => [
                'is_desktop' => $agent->isDesktop(),
                'is_mobile' => $agent->isMobile(),
                'is_tablet' => $agent->isTablet(),
                'platform' => $agent->platform() ?: 'Unknown',
                'browser' => $agent->browser() ?: 'Unknown',
            ],
            'ip_address' => $this->ip_address,
            'is_current_device' => $this->id === $request->session()->getId(),
            'last_active' => Carbon::createFromTimestamp($this->last_activity)->diffForHumans(),
        ];
    }
}
