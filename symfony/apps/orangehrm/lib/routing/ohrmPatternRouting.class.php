<?php

class ohrmPatternRouting extends sfPatternRouting {

    protected function getRouteThatMatchesUrl($url) {

        if (
            (preg_match_all("/\.html/i", $url) == 1 || preg_match_all("/\.rss/i", $url) == 1) &&
            preg_match_all("/(\.)+/", $url) == 1
        ) {
            if (preg_match("/\.html(.)+/i", $url) || preg_match("/\.rss(.)+/i", $url)) {
                return false;
            }
        } else if (preg_match("/(.)*\.(.)*/", $url) || preg_match("/((.)*~)$/", $url)) {
            return false;
        }

        foreach ($this->routes as $name => $route) {
            $route = $this->getRoute($name);
            $parameters = $route->matchesUrl($url, $this->options['context']);

            if (false === $parameters) {
                continue;
            }

            if (!preg_match("/^([a-z,A-Z]+)$/", $parameters['action'])) {
                return false;
            }

            if (
                !preg_match("/^([a-z,A-Z]+)$/", $parameters['module']) &&
                !preg_match("/^api([a-z,A-Z,0-9]+)$/", $parameters['module'])
            ) {
                return false;
            }

            return array('name' => $name, 'pattern' => $route->getPattern(), 'parameters' => $parameters);
        }

        return false;
    }

}
