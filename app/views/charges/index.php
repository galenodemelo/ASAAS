<div class="app__box shadowed">
	<h1><?= self::$v['h1'] ?></h1>

	<!-- Add or filter -->
	<div class="app__controls">

		<!-- Add new -->
		<a href="<?= BASE_PATH . 'charges/new/' ?>" class="app__controls__action mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
			Nova cobrança
		</a>

		<!-- Filter results -->
		<form class="app__controls__filters" id="charges-search" method="GET" action="<?= BASE_PATH ?>charges/search/">

			<!-- Name or email -->
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input class="mdl-textfield__input" type="search" id="name_email" name="name_email" value="<?= self::$v['search_parameters']['name_email'] ?? '' ?>">
				<label class="mdl-textfield__label" for="name_email">Buscar por nome ou e-mail...</label>
			</div>

			<!-- Initial date -->
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input class="mdl-textfield__input" type="search" id="initial_date" name="initial_date" pattern="\d{2}\/\d{2}\/\d{4}" data-mask="99/99/9999" maxlength="10" value="<?= self::$v['search_parameters']['initial_date'] ?? '' ?>">
				<label class="mdl-textfield__label" for="initial_date">Data de início</label>
			</div>

			<!-- Final date -->
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<input class="mdl-textfield__input" type="search" id="final_date" name="final_date" pattern="\d{2}\/\d{2}\/\d{4}" data-mask="99/99/9999" maxlength="10" value="<?= self::$v['search_parameters']['final_date'] ?? '' ?>">
				<label class="mdl-textfield__label" for="final_date">Data final</label>
			</div>

			<!-- Final date -->
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<select class="mdl-textfield__input" name="payment_methods">
					<option value="">Forma de pagamento</option>

					<?php foreach (self::$v['payment_methods_list'] as $payment_method) : ?>

						<?php if (!empty(self::$v['search_parameters']['payment_methods']) && $payment_method->id == self::$v['search_parameters']['payment_methods']) : ?>
							<option value="<?= $payment_method->id ?>" selected><?= $payment_method->description ?></option>
						<?php else : ?>
							<option value="<?= $payment_method->id ?>"><?= $payment_method->description ?></option>
						<?php endif; ?>

					<?php endforeach; ?>
				</select>
			</div>

			<!-- Final date -->
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<select class="mdl-textfield__input" name="status">
					<option value="">Status</option>

					<?php foreach (self::$v['charges_status_list'] as $status) : ?>

						<?php if (!empty(self::$v['search_parameters']['status']) && $status->id == self::$v['search_parameters']['status']) : ?>
							<option value="<?= $status->id ?>" selected><?= $status->description ?></option>
						<?php else : ?>
							<option value="<?= $status->id ?>"><?= $status->description ?></option>
						<?php endif; ?>

					<?php endforeach; ?>
				</select>
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
				<th class="mdl-data-table__cell--non-numeric">Vencimento</th>
				<th class="mdl-data-table__cell--non-numeric">Forma de pagamento</th>
				<th class="mdl-data-table__cell--non-numeric">Status</th>
				<th class="mdl-data-table__cell--non-numeric">Pago em</th>
				<th class="mdl-data-table__cell--non-numeric">Descrição</th>
				<th>Valor</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty(self::$v['charges_list'])) : ?>
				<!-- Empty results -->
				<tr>
					<td class="mdl-data-table__cell--non-numeric" colspan="6">Nenhuma cobrança encontrada</td>
				</tr>
			<?php else : ?>

				<!-- Iterates over all the items on list -->
				<?php foreach (self::$v['charges_list'] as $charge_item) : ?>
					<tr>
						<td class="mdl-data-table__cell--non-numeric"><?= $charge_item->customer ?></td>
						<td class="mdl-data-table__cell--non-numeric"><?= \Classes\Helpers::formatDate($charge_item->due_date) ?></td>
						<td class="mdl-data-table__cell--non-numeric"><?= $charge_item->payment_method ?></td>
						<td class="mdl-data-table__cell--non-numeric"><?= $charge_item->status ?></td>
						<td class="mdl-data-table__cell--non-numeric"><?= \Classes\Helpers::formatDate($charge_item->payment_date) ?></td>
						<td class="mdl-data-table__cell--non-numeric"><?= $charge_item->description ?></td>
						<td><?= \Classes\Helpers::formatMoney($charge_item->value) ?></td>
						<td>
							<?php if (empty($charge_item->payment_date)) : ?>
								<!-- Mark as paid button -->
								<a href="<?= BASE_PATH . 'charges/paid/' . $charge_item->id ?>" id="<?= 'bto-charge_paid-' . $charge_item->id ?>" class="mdl-button mdl-js-button mdl-button--icon mdl-button--colored">
									<i class="material-icons">check</i>
								</a>
								<div class="mdl-tooltip mdl-tooltip--large" for="<?= 'bto-charge_paid-' . $charge_item->id ?>">
									Marcar como pago
								</div>

								<!-- Edit button -->
								<a href="<?= BASE_PATH . 'charges/edit/' . $charge_item->id ?>" id="<?= 'bto-charge_edit-' . $charge_item->id ?>" class="mdl-button mdl-js-button mdl-button--icon">
									<i class="material-icons">edit</i>
								</a>
								<div class="mdl-tooltip mdl-tooltip--large" for="<?= 'bto-charge_edit-' . $charge_item->id ?>">
									Editar cobrança
								</div>

								<!-- Delete button -->
								<a href="<?= BASE_PATH . 'charges/delete/' . $charge_item->id ?>" id="<?= 'bto-charge_delete-' . $charge_item->id ?>" class="mdl-button mdl-js-button mdl-button--icon" ask-for-confirmation="Tem certeza que deseja deletar a cobrança de <?= $charge_item->customer ?>?">
									<i class="material-icons">delete</i>
								</a>
								<div class="mdl-tooltip mdl-tooltip--large" for="<?= 'bto-charge_delete-' . $charge_item->id ?>">
									Excluir cobrança
								</div>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>

			<?php endif; ?>
		</tbody>
	</table>

	<?= self::$v['pagination'] ?? '' ?>
</div>