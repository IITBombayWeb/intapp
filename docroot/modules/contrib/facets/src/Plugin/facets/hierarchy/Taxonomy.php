<?php

namespace Drupal\facets\Plugin\facets\hierarchy;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\Condition;
use Drupal\facets\Hierarchy\HierarchyPluginBase;
<<<<<<< HEAD
=======
use Drupal\taxonomy\TermStorageInterface;
>>>>>>> origin/development
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Taxonomy hierarchy.
 *
 * @FacetsHierarchy(
 *   id = "taxonomy",
 *   label = @Translation("Taxonomy hierarchy"),
 *   description = @Translation("Hierarchy structure provided by the taxonomy module.")
 * )
 */
class Taxonomy extends HierarchyPluginBase {

  /**
<<<<<<< HEAD
   * The current primary database.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;
=======
   * The term storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  protected $termStorage;
>>>>>>> origin/development

  /**
   * Constructs a Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
<<<<<<< HEAD
   * @param \Drupal\Core\Database\Connection $database
   *   The current primary database.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
=======
   * @param \Drupal\taxonomy\TermStorageInterface $termStorage
   *   The term storage.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TermStorageInterface $termStorage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->termStorage = $termStorage;
>>>>>>> origin/development
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
<<<<<<< HEAD
      $container->get('database')
=======
      $container->get('entity_type.manager')->getStorage('taxonomy_term')
>>>>>>> origin/development
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getParentIds($id) {
    $current_tid = $id;
    while ($parent = $this->taxonomyGetParent($current_tid)) {
      $current_tid = $parent;
      $parents[$id][] = $parent;
    }
    return isset($parents[$id]) ? $parents[$id] : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getNestedChildIds($id) {
<<<<<<< HEAD
    $children = &drupal_static(__FUNCTION__, []);
    if (!isset($children[$id])) {
      $query = $this->database->select('taxonomy_term_hierarchy', 'h');
      $query->addField('h', 'tid');
      $query->condition('h.parent', $id);
      $queried_children = $query->execute()->fetchCol();
      $subchilds = [];
      foreach ($queried_children as $child) {
        $subchilds = array_merge($subchilds, $this->getNestedChildIds($child));
      }
      $children[$id] = array_merge($queried_children, $subchilds);
=======
    $children = $this->termStorage->loadChildren($id);
    $children = array_filter(array_values(array_map(function ($it) {
      return $it->id();
    }, $children)));

    $subchilds = [];
    foreach ($children as $child) {
      $subchilds = array_merge($subchilds, $this->getNestedChildIds($child));
>>>>>>> origin/development
    }
    return array_merge($children, $subchilds);
  }

  /**
   * {@inheritdoc}
   */
  public function getChildIds(array $ids) {
<<<<<<< HEAD
    $result = $this->database->select('taxonomy_term_hierarchy', 'th')
      ->fields('th', ['tid', 'parent'])
      ->condition('th.parent', '0', '>')
      ->condition((new Condition('OR'))
        ->condition('th.tid', $ids, 'IN')
        ->condition('th.parent', $ids, 'IN')
      )
      ->execute();

    $parents = [];
    foreach ($result as $record) {
      $parents[$record->parent][] = $record->tid;
=======
    $parents = [];
    foreach ($ids as $id) {
      $terms = $this->termStorage->loadChildren($id);
      $parents[$id] = array_filter(array_values(array_map(function ($it) {
        return $it->id();
      }, $terms)));
>>>>>>> origin/development
    }
    $parents = array_filter($parents);
    return $parents;
  }

  /**
   * Returns the parent tid for a given tid, or false if no parent exists.
   *
   * @param int $tid
   *   A taxonomy term id.
   *
   * @return int|false
   *   Returns FALSE if no parent is found, else parent tid.
   */
  protected function taxonomyGetParent($tid) {
<<<<<<< HEAD
    $parent = &drupal_static(__FUNCTION__, []);

    if (!isset($parent[$tid])) {
      $query = $this->database->select('taxonomy_term_hierarchy', 'h');
      $query->addField('h', 'parent');
      $query->condition('h.tid', $tid);
      $parent[$tid] = $query->execute()->fetchField();
=======
    $parents = $this->termStorage->loadParents($tid);
    if (empty($parents)) {
      return FALSE;
>>>>>>> origin/development
    }
    return reset($parents)->id();
  }

}
