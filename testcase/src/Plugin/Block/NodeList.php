<?php

namespace Drupal\testcase\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

  /**
   * NodeList constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RendererInterface $renderer, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->renderer = $renderer;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
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
    $storage = $this->entityTypeManager->getStorage('node')->getQuery();
    $nids = $storage->sort('type')->execute();
    $nodes = Node::loadMultiple($nids);

    return $nodes;
  }

}
