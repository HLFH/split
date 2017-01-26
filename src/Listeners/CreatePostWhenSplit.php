<?php

namespace Flagrow\Split\Listeners;

use Flagrow\Split\Events\DiscussionWasSplit;
use Flagrow\Split\Posts\DiscussionSplitPost;
use Flarum\Event\ConfigurePostTypes;
use Illuminate\Events\Dispatcher;

class CreatePostWhenSplit
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(ConfigurePostTypes::class, [$this, 'addPostType']);
        $events->listen(DiscussionWasSplit::class, [$this, 'whenDiscussionWasSplit']);
    }

    /**
     * @param ConfigurePostTypes $event
     */
    public function addPostType(ConfigurePostTypes $event)
    {
        $event->add(DiscussionSplitPost::class);
    }

    /**
     * @param DiscussionWasSplit $event
     */
    public function whenDiscussionWasSplit(DiscussionWasSplit $event)
    {
        // post event on original discussion
        DiscussionSplitPost::reply(
            $event->originalDiscussion,
            $event->newDiscussion,
            $event->actor,
            $event->posts
        );
        // post event on new discussion
        DiscussionSplitPost::reply(
            $event->newDiscussion,
            $event->originalDiscussion,
            $event->actor,
            $event->posts
        );
    }
}
