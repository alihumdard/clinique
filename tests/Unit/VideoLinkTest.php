<?php

namespace Tests\Unit;

use App\Support\VideoLink;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The editor pastes whatever address the provider handed them, so every
 * shape a share button produces has to resolve to the same id.
 */
class VideoLinkTest extends TestCase
{
    /** @return array<string, array{string, string, string}> */
    public static function addresses(): array
    {
        return [
            'watch' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'youtube', 'dQw4w9WgXcQ'],
            'watch with extras' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=42s&list=PL1', 'youtube', 'dQw4w9WgXcQ'],
            'share link' => ['https://youtu.be/dQw4w9WgXcQ', 'youtube', 'dQw4w9WgXcQ'],
            'share link with time' => ['https://youtu.be/dQw4w9WgXcQ?t=90', 'youtube', 'dQw4w9WgXcQ'],
            'embed' => ['https://www.youtube.com/embed/dQw4w9WgXcQ', 'youtube', 'dQw4w9WgXcQ'],
            'nocookie embed' => ['https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', 'youtube', 'dQw4w9WgXcQ'],
            'shorts' => ['https://www.youtube.com/shorts/dQw4w9WgXcQ', 'youtube', 'dQw4w9WgXcQ'],
            'live' => ['https://www.youtube.com/live/dQw4w9WgXcQ', 'youtube', 'dQw4w9WgXcQ'],
            'no www' => ['https://youtube.com/watch?v=dQw4w9WgXcQ', 'youtube', 'dQw4w9WgXcQ'],
            'underscore and dash id' => ['https://youtu.be/a_b-c1D2e3F', 'youtube', 'a_b-c1D2e3F'],
            'vimeo' => ['https://vimeo.com/123456789', 'vimeo', '123456789'],
            'vimeo player' => ['https://player.vimeo.com/video/123456789', 'vimeo', '123456789'],
        ];
    }

    #[DataProvider('addresses')]
    public function test_it_parses_a_pasted_address(string $url, string $provider, string $id): void
    {
        $this->assertSame(
            ['provider' => $provider, 'provider_id' => $id],
            VideoLink::parse($url)
        );
    }

    /** @return array<string, array{?string}> */
    public static function rejected(): array
    {
        return [
            'empty' => [''],
            'null' => [null],
            'not a provider' => ['https://example.com/video.mp4'],
            'youtube channel, not a video' => ['https://www.youtube.com/@cliniquefsjp'],
            'youtube id too short' => ['https://youtu.be/abc'],
            'vimeo without an id' => ['https://vimeo.com/cliniquefsjp'],
        ];
    }

    #[DataProvider('rejected')]
    public function test_it_rejects_an_address_it_cannot_embed(?string $url): void
    {
        $this->assertNull(VideoLink::parse($url));
    }

    public function test_it_offers_a_channel_thumbnail_for_youtube_only(): void
    {
        $this->assertSame(
            'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
            VideoLink::thumbnail('youtube', 'dQw4w9WgXcQ')
        );

        // Vimeo needs an API call for its thumbnail, so there is none to give.
        $this->assertNull(VideoLink::thumbnail('vimeo', '123456789'));
        $this->assertNull(VideoLink::thumbnail('youtube', null));
    }
}
