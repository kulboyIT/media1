<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Utility\Inflector;

$associations += ['BelongsTo' => [], 'HasOne' => [], 'HasMany' => [], 'BelongsToMany' => []];
$immediateAssociations = $associations['BelongsTo'] + $associations['HasOne'];
$associationFields = collection($fields)
    ->map(function($field) use ($immediateAssociations) {
        foreach ($immediateAssociations as $alias => $details) {
            if ($field === $details['foreignKey']) {
                return [$field => $details];
            }
        }
    })
    ->filter()
    ->reduce(function($fields, $value) {
        return $fields + $value;
    }, []);

$groupedFields = collection($fields)
    ->filter(function($field) use ($schema) {
        return $schema->columnType($field) !== 'binary';
    })
    ->groupBy(function($field) use ($schema, $associationFields) {
        $type = $schema->columnType($field);
        if (isset($associationFields[$field])) {
            return 'string';
        }
        if (in_array($type, ['integer', 'float', 'decimal', 'biginteger'])) {
            return 'number';
        }
        if (in_array($type, ['date', 'time', 'datetime', 'timestamp'])) {
            return 'date';
        }
        return in_array($type, ['text', 'boolean']) ? $type : 'string';
    })
    ->toArray();

$groupedFields += ['number' => [], 'string' => [], 'boolean' => [], 'date' => [], 'text' => []];
$pk = "\$$singularVar->{$primaryKey[0]}";
?>
<nav class="navbar navbar-default">
  <div class="container-fluid">
      <ul class="nav navbar-nav">
        <li><CakePHPBakeOpenTag= $this->Html->link(__('Editer <?= $singularHumanName ?>'), ['action' => 'edit', <?= $pk ?>]) CakePHPBakeCloseTag> </li>
        <li><CakePHPBakeOpenTag= $this->Form->postLink(__('Supprimer <?= $singularHumanName ?>'), ['action' => 'delete', <?= $pk ?>], ['confirm' => __('??tes vous s??r de vouloir supprimer # {0}?', <?= $pk ?>)]) CakePHPBakeCloseTag> </li>
        <li><CakePHPBakeOpenTag= $this->Html->link(__('Lister <?= $pluralHumanName ?>'), ['action' => 'index']) CakePHPBakeCloseTag> </li>
        <li><CakePHPBakeOpenTag= $this->Html->link(__('Nouveau <?= $singularHumanName ?>'), ['action' => 'add']) CakePHPBakeCloseTag> </li>
<?php
    $done = [];
    foreach ($associations as $type => $data) {
        foreach ($data as $alias => $details) {
            if ($details['controller'] !== $this->name && !in_array($details['controller'], $done)) {
?>
        <li><CakePHPBakeOpenTag= $this->Html->link(__('Lister <?= $this->_pluralHumanName($alias) ?>'), ['controller' => '<?= $details['controller'] ?>', 'action' => 'index']) CakePHPBakeCloseTag> </li>
        <li><CakePHPBakeOpenTag= $this->Html->link(__('Nouveau <?= Inflector::humanize(Inflector::singularize(Inflector::underscore($alias))) ?>'), ['controller' => '<?= $details['controller'] ?>', 'action' => 'add']) CakePHPBakeCloseTag> </li>
<?php
                $done[] = $details['controller'];
            }
        }
    }
?>
    </ul>
  </div>
</nav>
<div class="well">
    <table class="table table-striped table-condensed table-responsive">
<?php if ($groupedFields['string']) : ?>
<?php foreach ($groupedFields['string'] as $field) : ?>
<?php if (isset($associationFields[$field])) :
            $details = $associationFields[$field];
?>
        <tr>
            <th><CakePHPBakeOpenTag= __('<?= Inflector::humanize($details['property']) ?>') CakePHPBakeCloseTag></th>
            <td><CakePHPBakeOpenTag= $<?= $singularVar ?>->has('<?= $details['property'] ?>') ? $this->Html->link($<?= $singularVar ?>-><?= $details['property'] ?>-><?= $details['displayField'] ?>, ['controller' => '<?= $details['controller'] ?>', 'action' => 'view', $<?= $singularVar ?>-><?= $details['property'] ?>-><?= $details['primaryKey'][0] ?>]) : '' CakePHPBakeCloseTag></td>
        </tr>
<?php else : ?>
        <tr>
            <th><CakePHPBakeOpenTag= __('<?= Inflector::humanize($field) ?>') CakePHPBakeCloseTag></th>
            <td><CakePHPBakeOpenTag= h($<?= $singularVar ?>-><?= $field ?>) CakePHPBakeCloseTag></td>
        </tr>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
<?php if ($groupedFields['number']) : ?>
<?php foreach ($groupedFields['number'] as $field) : ?>
        <tr>
            <th><CakePHPBakeOpenTag= __('<?= Inflector::humanize($field) ?>') CakePHPBakeCloseTag></th>
            <td><CakePHPBakeOpenTag= $this->Number->format($<?= $singularVar ?>-><?= $field ?>) CakePHPBakeCloseTag></td>
        </tr>
<?php endforeach; ?>
<?php endif; ?>
<?php if ($groupedFields['date']) : ?>
<?php foreach ($groupedFields['date'] as $field) : ?>
        <tr>
            <th><?= "<?= __('" . Inflector::humanize($field) . "') ?>" ?></th>
            <td><CakePHPBakeOpenTag= h($<?= $singularVar ?>-><?= $field ?>) CakePHPBakeCloseTag></td>
        </tr>
<?php endforeach; ?>
<?php endif; ?>
<?php if ($groupedFields['boolean']) : ?>
<?php foreach ($groupedFields['boolean'] as $field) : ?>
        <tr>
            <th><CakePHPBakeOpenTag= __('<?= Inflector::humanize($field) ?>') CakePHPBakeCloseTag></th>
            <td><CakePHPBakeOpenTag= $<?= $singularVar ?>-><?= $field ?> ? __('Yes') : __('No'); CakePHPBakeCloseTag></td>
         </tr>
<?php endforeach; ?>
<?php endif; ?>
    </table>
<?php if ($groupedFields['text']) : ?>
<?php foreach ($groupedFields['text'] as $field) : ?>


          <h4><CakePHPBakeOpenTag= __('<?= Inflector::humanize($field) ?>') CakePHPBakeCloseTag></h4>
          <CakePHPBakeOpenTag= $this->Text->autoParagraph(h($<?= $singularVar ?>-><?= $field ?>)); CakePHPBakeCloseTag>
          <br>


<?php endforeach; ?>
<?php endif; ?>
<?php
$relations = $associations['HasMany'] + $associations['BelongsToMany'];
foreach ($relations as $alias => $details):
    $otherSingularVar = Inflector::variable($alias);
    $otherPluralHumanName = Inflector::humanize(Inflector::underscore($details['controller']));
    ?>
    <div class="related">
        <h4><CakePHPBakeOpenTag= __('Related <?= $otherPluralHumanName ?>') CakePHPBakeCloseTag></h4>
        <CakePHPBakeOpenTagphp if (!empty($<?= $singularVar ?>-><?= $details['property'] ?>)): CakePHPBakeCloseTag>
        <table cellpadding="0" cellspacing="0">
            <tr>
<?php foreach ($details['fields'] as $field): ?>
                <th><CakePHPBakeOpenTag= __('<?= Inflector::humanize($field) ?>') CakePHPBakeCloseTag></th>
<?php endforeach; ?>
                <th class="actions"><CakePHPBakeOpenTag= __('Actions') CakePHPBakeCloseTag></th>
            </tr>
            <CakePHPBakeOpenTagphp foreach ($<?= $singularVar ?>-><?= $details['property'] ?> as $<?= $otherSingularVar ?>): CakePHPBakeCloseTag>
            <tr>
<?php foreach ($details['fields'] as $field): ?>
                <td><CakePHPBakeOpenTag= h($<?= $otherSingularVar ?>-><?= $field ?>) CakePHPBakeCloseTag></td>
<?php endforeach; ?>
<?php $otherPk = "\${$otherSingularVar}->{$details['primaryKey'][0]}"; ?>
                <td class="actions">
                    <CakePHPBakeOpenTag= $this->Html->link(__('View'), ['controller' => '<?= $details['controller'] ?>', 'action' => 'view', <?= $otherPk ?>]) ?>

                    <CakePHPBakeOpenTag= $this->Html->link(__('Edit'), ['controller' => '<?= $details['controller'] ?>', 'action' => 'edit', <?= $otherPk ?>]) ?>

                    <CakePHPBakeOpenTag= $this->Form->postLink(__('Delete'), ['controller' => '<?= $details['controller'] ?>', 'action' => 'delete', <?= $otherPk ?>], ['confirm' => __('Are you sure you want to delete # {0}?', <?= $otherPk ?>)]) ?>

                </td>
            </tr>
            <CakePHPBakeOpenTagphp endforeach; CakePHPBakeCloseTag>
        </table>
    <CakePHPBakeOpenTagphp endif; CakePHPBakeCloseTag>
    </div>
<?php endforeach; ?>
</div>
