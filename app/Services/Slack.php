<?php
namespace CodeDay\Clear\Services;

use GuzzleHttp;

/**
 * Supports sending messages to Slack.
 *
 * Contains functionality to send messages to the Slack configured in the local settings.
 *
 * @package     CodeDay\Clear\Services
 * @author      Tyler Menezes <tylermenezes@studentrnd.org>
 * @copyright   (c) 2014-2015 StudentRND
 * @license     Perl Artistic License 2.0
 */
class Slack {
    private static $defaults = [
        'icon_emoji' => ':codeday:',
        'channel' => '#events',
        'username' => 'clear'
    ];

    protected static $client;

    public static function GetOauthAccess($code)
    {
        try {
            $response = self::$client->get('https://slack.com/api/oauth.access', [ 'query' => [
                'client_id' => \Config::get('slack.client_id'),
                'client_secret' => \Config::get('slack.client_secret'),
                'redirect_uri' => "https://clear.codeday.org/api/slack/oauth",
                'code' => $code
            ]]);
        } catch (\Exception $ex) { return null; }

        if ($response->getStatusCode() == 202) {
            return false;
        } elseif ($response->getStatusCode() != 200) {
            return null;
        } else {
            return json_decode($response->getBody());
        }
    }

    /**
     * Sends a payload (asynchronously) to a certain URL on the Slack API.
     *
     * @param array $payload    Associative array containing the payload data.
     * @param string $url       The URL to send the payload to.
     */
    public static function SendPayloadToUrl($payload, $url)
    {
        // $payload = array_merge(self::$defaults, $payload);

        \Queue::push(function($job) use ($payload, $url)
        {
            $ch = \curl_init($url);
            \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            \curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            \curl_exec($ch);
            \curl_close($ch);

            $job->delete();
        });
    }

    /**
     * Sends a payload (asynchronously) to the Slack servers.
     *
     * A payload is a low-level Slack API object which contains data about an action to be performed. See the Slack
     * API docs for more details, or use the Slack::Message function to send a simple message.
     *
     * @param array $payload    Associative array containing the payload data.
     */
    public static function SendPayload($payload)
    {
        $payload = array_merge(self::$defaults, $payload);
        $url = \Config::get('slack.webhook_url');

        \Queue::push(function($job) use ($payload, $url)
        {
            $ch = \curl_init($url);
            \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            \curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            \curl_exec($ch);
            \curl_close($ch);

            $job->delete();
        });
    }

    /**
     * Sends a message to a Slack room.
     *
     * Sends a text message to a room in the Slack set in the local config. If the room is not otherwise specified, it
     * will default to the room set in the Slack webhook that was created. Text supports Slack formatting, see:
     * https://api.slack.com/docs/formatting
     *
     * @param string    $text   Message to send. No HTML is supported, but does support Slack formatting.
     * @param string    $to     (Optional)
     */
    public static function Message($text, $to = null)
    {
        $payload = [
            'text' => $text
        ];
        if (isset($to)) {
            $payload['channel'] = $to;
        }

        self::SendPayload($payload);
    }

    public static function booting()
    {
        self::$client = new GuzzleHttp\Client([]);
    }
}

Slack::booting();
