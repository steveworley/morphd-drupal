<?php
/**
 * @file
 * Build a query-able interface into the entity.
 */

namespace Drupal\morphd\Query;

use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Serialization\Json;

abstract class RestEntityQueryBase {

  abstract protected function getOperators();
  abstract protected function opEquals($field = '', $value = '');
  abstract protected function opNotEquals($field = '', $value = '');
  abstract protected function opIn($field = '', $value = []);
  abstract protected function opNotIn($field = '', $value = []);
  abstract protected function opGreaterThan($field = '', $value = '');
  abstract protected function opGreaterThanEqual($field = '', $value = '');
  abstract protected function opLessThan($field = '', $value = '');
  abstract protected function opLessThanEqual($field = '', $value = '');

  public function __construct(Request $request, $container) {
    $type = 'node'; // Get the entity type from the request.

    $this->query = \Drupal::entityQuery($type);
    $this->storage = $container->get('entity.manager')->getStorage($type);

    $this->container = $container;

    $this->params = $this->getRequestParams($request);
  }

  protected function setLastError($code, $message) {
    if (empty($this->error)) {
      $this->error = ['error' => $code, 'error_description' => $message];
    }
    return $this;
  }

  public function hasError() {
    return !empty($this->error);
  }

  public function getLastError() {
    return $this->error;
  }

  protected function getRequestParams(Request $request) {
    $params = array_merge(['where' => [], 'limit' => null], $request->query->all());
    $params['where'] = Json::decode($params['where']);

    ksort($params['where']);

    return $params;
  }

  public function operators() {
    return $this->getOperators() + [
      '$equals' => ['_method' => 'opEquals'],
      '$nequals' => ['_method' => 'opNotEquals'],
      '$in' => ['_method' => 'opIn'],
      '$nin' => ['_method' => 'opNotIn'],
      '$gt' => ['_method' => 'opGreaterThan'],
      '$gte' => ['_method' => 'opGreaterThanEqual'],
      '$lt' => ['_method' => 'opLessThan'],
      '$lte' => ['_method' => 'opLessThanEqual'],
    ];
  }

  protected function buildQuery() {
    $operators = $this->operators();

    foreach ($this->params['where'] as $field => $condition) {
      $op = '$equals';

      if (is_array($condition)) {
        $op = key($condition);
        $condition = $condition[$op];
      }

      if (empty($operators[$op])) {
        $this->setLastError('invalid_operator', "$op is an invalid operator");
        continue;
      }

      call_user_func_array(
        [$this, $operators[$op]['_method']],
        [$field, $condition]
      );
    }

    return $this;
  }

  public function execute() {
    $this->buildQuery();
    $results = $this->query->execute();

    if (empty($results)) {
      $this->setLastError('not_found', 'Query yielded no results.');
    }

    return $this->storage->loadMultiple($results);
  }

}
