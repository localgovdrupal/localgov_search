<?php

namespace Drupal\localgov_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\views\Form\ViewsExposedForm;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'SitewideSearchBlock' block.
 *
 * @Block(
 *  id = "localgov_sitewide_search_block",
 *  admin_label = @Translation("Sitewide search block"),
 * )
 */
class SitewideSearchBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder')
    );
  }

  /**
   * Sitewide search block constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = [];

    // Add sitewide search view filters to block.
    // Adapted from: https://blog.werk21.de/en/2017/03/08/programmatically-render-exposed-filter-form
    $view_id = 'localgov_sitewide_search';
    $display_id = 'sitewide_search_page';
    $view = Views::getView($view_id);

    if ($view) {
      $view->setDisplay($display_id);
      $view->initHandlers();
      $form_state = (new FormState())->setStorage([
        'view' => $view,
        'display' => &$view->display_handler->display,
        'rerender' => TRUE,
      ])
        ->setMethod('get')
        ->setAlwaysProcess()
        ->disableRedirect();
      $form_state->set('rerender', NULL);
      $form = $this->formBuilder->buildForm(ViewsExposedForm::class, $form_state);
      $form['#id'] .= '-block';
      $form['s']['#attributes']['placeholder'] = 'Search';
      $form['s']['#required'] = TRUE;
    }

    return $form;
  }

}
