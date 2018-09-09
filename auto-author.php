<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Plugin\Admin\Admin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class AutoAuthorPlugin
 * @package Grav\Plugin
 */
class AutoAuthorModPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onAdminCreatePageFrontmatter' => ['onAdminCreatePageFrontmatter', 0]
        ];
    }
    
    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    function get_gravatar( $email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = array() ) {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }

    /**
     * Initialize the plugin
     *
     * @param Event $event
     */
    public function onAdminCreatePageFrontmatter(Event $event)
    {
        $header = $event['header'];
        if (!isset($header['author'])) {
            if ($this->config->get('plugins.auto-author.user')) {
                $admin = $this->grav['admin'];
                $mail = $admin->user->get('email');

                $author = $this->grav['user']->username;
                $avatar = $this->get_gravatar( $mail, 256, 'identicon', 'g', false );
            } else {
                $author = $this->grav['config']->get('site.author.name');
            }
            $header['author'] = $author;
            $header['avatar'] = $avatar;
            $event['header'] = $header;
        }
    }
}
