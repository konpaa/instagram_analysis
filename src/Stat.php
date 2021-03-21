<?php

namespace instagramPhp\Stat;

use Illuminate\Support\Collection;
use function instagramPhp\Storage\getUserData;

function getUserStat(string $username): array
{
    $data = getUserData($username);
    if ($data === null) {
        throw new \Exception("Data not found for {$username}");
    }

    return getStatFormUserData($data);
}

function getStatFormUserData(\stdClass $data): array
{
    $stat = [];
    $followers = $data->graphql->user->edge_followed_by->count;
    $stat['followers'] = $followers;
    $stat['posts'] = [];

    foreach ($data->graphql->user->edge_owner_to_timeline_media->edges as $nodeData) {
        $node = $nodeData->node;
        $comments = $node->edge_media_to_comment->count;
        $likes = $node->edge_liked_by->count;
        $engagements = $comments + $likes;
        $er = null;
        if ($followers > 0) {
            $er = round($engagements / $followers * 100, 2);
        }

        $post = [
            'comments' => $comments,
            'likes' => $likes,
            'engagements' => $engagements,
            'er' => $er,
            'url' => "https://www.instagram.com/p/{$node->shortcode}",
        ];

        $stat['posts'][] = $post;

    }
    if ($followers > 0) {
        $stat['avgEr'] = getAverageEngagementRate($stat['posts'], $followers);
    }

    $stat['mostLikedPost'] = findTopPost($stat['posts'], 'likes');
    $stat['mostCommentedPost'] = findTopPost($stat['posts'], 'comments');
    $stat['topEr'] = findTopPost($stat['posts'], 'er');


    return $stat;
}

function getAverageEngagementRate(array $posts, int $followers): float
{

    $cb = function ($engs, $post) {
        return $engs + $post['engagements'];
    };
    $totalEngs =  array_reduce(
        $posts,
        $cb,
        0);

    return round($totalEngs / $followers / count($posts) * 100, 2);
}

function findTopPost(array $posts, $property): array
{
    $collection = new Collection($posts);

    return $collection->sortByDesc($property)->first();
}