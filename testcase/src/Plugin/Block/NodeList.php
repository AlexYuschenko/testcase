<?php

namespace Drupal\testcase\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Renderer;
use Drupal\node\Entity\Node;

/**
 * Provides list of Nodes grouped by content type.
 *
 * @Block(
 *   id = "testcase_node_list",
 *   admin_label = @Translation("Node List"),
 * )
 */
class NodeList extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  protected $renderer;

  protected $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, Renderer $renderer, EntityTypeManager $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->renderer = $renderer;
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('renderer'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $nodes = $this->getNodeList();

    $content = [];
    foreach ($nodes as $key => $node) {
      $content[] = [
        '#markup' => $node->label() . ' (' . $node->id() . ')' . ' (' . $node->bundle() . ')',
        '#type' => 'item',
      ];
    }

    return [
      '#markup' => $this->renderer->render($content),
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  private function getNodeList() {
    $storage = $this->entityTypeManager->getStorage('node');
    //    $nids = $storage->sort('type')->execute();
    $nodes = [];
    //    $nodes = Node::loadMultiple($nids);

    return $nodes;
  }

}
