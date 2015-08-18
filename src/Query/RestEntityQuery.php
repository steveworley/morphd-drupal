<?php

/**
 * @file
 * Define the REST entity query.
 */

namespace Drupal\morphd\Query;

use Drupal\Core\Database\Query\Select;

class RestEntityQuery extends RestEntityQueryBase {

  use RestEntityQueryTrait;

  /**
   * Extend the list of available operators.
   *
   * @return array
   *   Add operators to the list.
   */
  protected function getOperators() {
    // We're not going to extend the base operators.
    return [
      '$like' => ['_method' => 'opLike'],
    ];
  }

  /**
   * Perform a like query.
   *
   * @param string $field
   *   The field to perform the like on.
   * @param string $value
   *   The string value to match against.
   *
   * @return \Drupal\morphd\Query\RestEntityQuery
   */
  public function opLike($field = '', $value = '') {
    $value = $this->container->get('database')->escapeLike($value);
    $this->query->condition($field, "%{$value}%", 'LIKE');
    return $this;
  }

}
