<?php
/**
 *
 * User: hjone
 * Date: 20/12/2015
 * Time: 00:05
 */

namespace WebApp\base;

abstract class Page
{
    private $content;
    private $statusCode;
    private $title;

    /**
     * @return String - Page content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

}