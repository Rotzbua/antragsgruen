<?php

namespace app\async\protocol;

class InternalClient
{
    /**
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     */
    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        if ($request->server['remote_addr'] !== '127.0.0.1') {
            $response->status(403);
            $response->end('only localhost is allowed to post');
            return;
        }

        $request_uri = $request->server['request_uri'];

        if ($request->server['request_method'] === 'POST') {
            if (preg_match('/^\/(?<consultation>\d+)\/(?<channel>\w+)\/?$/siu', $request_uri, $matches)) {
                $channel = Channel::getSpoolFromId($matches['consultation'], $matches['channel']);
                $channel->sendToSessions($request->post);

                $response->status(201);
                $response->end('Notified all endpoints');
                return;
            }
        }

        $response->status(404);
        $response->end('Could not find the endpoint');
    }
}
