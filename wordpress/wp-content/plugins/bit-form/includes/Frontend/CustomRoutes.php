<?php

namespace BitCode\BitForm\Frontend;

use ReflectionMethod;

class CustomRoutes
{
  protected $routes = [];

  public function __construct()
  {
    add_action('parse_request',       [$this, 'parseRequestAction']);
    add_filter('rewrite_rules_array', [$this, 'rewriteRulesArrayFilter']);
  }

  public function parseRequestAction($query)
  {
    if ($query->matched_rule and isset($this->routes[$query->matched_rule])) {
      $route = $this->routes[$query->matched_rule];
      $this->methodExecution($route, $query);
      exit;
    }
  }

  public function add($match, $callback, $template = null, $query_vars = [])
  {
    $this->routes[$match] = compact('callback', 'template', 'query_vars');
  }

  public function rewriteRulesArrayFilter($rules)
  {
    $newRules = [];
    foreach ($this->routes as $match => $route) {
      $newRules[$match] = $this->makeRuleUrl($route);
    }
    return $newRules + $rules;
  }

  // public function addedQueryParmas($vars) {
  //   foreach ($this->routes as $route) {
  //     foreach ($route['query_vars'] as $key => $value) {
  //       $vars[] = $key;
  //     }
  //   }

  //   return $vars;
  // }

  protected function methodExecution($route, $query)
  {
    $params = [];

    foreach ($route['query_vars'] as $name => $match) {
      $params[] = $query->query_vars[$name];
    }

    if (is_array($route['callback']) && 2 === count($route['callback'])) {
      $className = $route['callback'][0];
      $method = $route['callback'][1];
      if (class_exists($className) && method_exists($className, $method)) {
        $reflectionMethod = new ReflectionMethod($className, $method);
        $reflectionMethod->invoke($reflectionMethod->isStatic() ? null : new $className());
      } else {
        return 'Method or class not exist';
      }
    } else {
      return 'Invalid invokeable';
    }

    // call_user_func_array($route['callback'], $params);
  }

  protected function makeRuleUrl($route)
  {
    $queryVars = [];
    foreach ($route['query_vars'] as $name => $match) {
      $queryVars[] = $name . '=$matches[' . $match . ']';
    }

    return 'index.php?' . implode('&', $queryVars);
  }
}
