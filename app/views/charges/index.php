<div class="app__box shadowed">
	<h1>Lista de cobranças</h1>

	<!-- Add or filter -->
	<div class="app__controls">

		<!-- Add new -->
		<a href="<?= BASE_PATH . 'charges/new/' ?>" class="app__controls__action mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
			Nova cobrança
		</a>

		<!-- Filter results -->
		<form class="app__controls__filters" id="charges-search" method="GET">

			<!-- Textfield with Floating Label -->
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input class="mdl-textfield__input" type="search" id="charges-name" name="s">
				<label class="mdl-textfield__label" for="charges-name">Buscar por nome ou e-mail...</label>
			</div>

			<!-- Colored icon button -->
			<button type="submit" class="mdl-button mdl-js-button mdl-button--icon mdl-button--colored">
				<i class="material-icons">search</i>
			</button>
		</form>

	</div>

	<!-- List -->
	<table class="app__table mdl-data-table mdl-js-data-table mdl-shadow--2dp">
		<thead>
			<tr>
				<th class="mdl-data-table__cell--non-numeric">Cliente</th>
				<th class="mdl-data-table__cell--non-numeric">Status</th>
				<th class="mdl-data-table__cell--non-numeric">Vencimento</th>
				<th class="mdl-data-table__cell--non-numeric">Descrição</th>
				<th>Valor</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($charges_list)) : ?>
				<!-- Empty results -->
				<tr>
					<td class="mdl-data-table__cell--non-numeric" colspan="6">Nenhuma cobrança encontrada</td>
				</tr>
			<?php else : ?>

				<!-- Iterates over all the items on list -->
				<?php foreach ($charges_list as $charge_item) : ?>
					<tr>
						<td class="mdl-data-table__cell--non-numeric"><?= $charge_item->customer ?></td>
						<td class="mdl-data-table__cell--non-numeric"><?= $charge_item->status ?></td>
						<td class="mdl-data-table__cell--non-numeric"><?= Helpers::formatDate($charge_item->due_date) ?></td>
						<td class="mdl-data-table__cell--non-numeric"><?= $charge_item->description ?></td>
						<td><?= Helpers::formatMoney($charge_item->value) ?></td>
						<td>

						</td>
					</tr>
				<?php endforeach; ?>

			<?php endif; ?>
		</tbody>
	</table>
</div>