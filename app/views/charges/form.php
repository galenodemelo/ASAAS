<div class="app__box shadowed">
	<h1><?= self::$v['h1'] ?? '' ?></h1>

	<form method="POST" action="<?= self::$v['form_action'] ?? '' ?>" id="form_charge" class="app__box__form">

		<!-- Customer name -->
		<div class="app__box__form__full-width mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			<input class="mdl-textfield__input" type="text" id="customer_name" name="customer_name" value="<?= self::$v['values']['customer_name'] ?? '' ?>" maxlength="255" autofocus>
			<label class="mdl-textfield__label" for="customer-name">Nome do cliente</label>
		</div>

		<!-- Customer email -->
		<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			<input class="mdl-textfield__input" type="email" id="customer_email" name="customer_email" value="<?= self::$v['values']['customer_email'] ?? '' ?>" pattern=".{3,}" maxlength="255">
			<label class="mdl-textfield__label" for="customer-email">E-mail</label>
			<span class="mdl-textfield__error">E-mail inválido</span>
		</div>

		<!-- Customer CPF -->
		<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			<input class="mdl-textfield__input" type="text" id="customer_cpf" name="customer_cpf" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" value="<?= self::$v['values']['customer_cpf'] ?? '' ?>" data-mask="999.999.999-99" maxlength="14">
			<label class="mdl-textfield__label" for="customer_cpf">CPF do cliente</label>
			<span class="mdl-textfield__error">CPF inválido</span>
		</div>

		<!-- Due Date -->
		<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			<input class="mdl-textfield__input" type="text" id="due_date" name="due_date" pattern="\d{2}\/\d{2}\/\d{4}" value="<?= self::$v['values']['due_date'] ?? '' ?>" data-mask="99/99/9999" maxlength="10">
			<label class="mdl-textfield__label" for="due_date">Data de vencimento</label>
			<span class="mdl-textfield__error">Vencimento inválido</span>
		</div>

		<!-- Payment methods -->
		<?php if (!empty(self::$v['payment_methods_list'])) : ?>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
				<select class="mdl-textfield__input" type="text" name="payment_methods" id="payment_methods">
					<option value="">Forma de pagamento</option>

					<?php foreach (self::$v['payment_methods_list'] as $payment_method) : ?>

						<?php if (!empty(self::$v['values']['payment_method']) && $payment_method->id === self::$v['values']['payment_method']) : ?>
							<option value="<?= $payment_method->id ?>" selected><?= $payment_method->description ?></option>
						<?php else : ?>
							<option value="<?= $payment_method->id ?>"><?= $payment_method->description ?></option>
						<?php endif; ?>

					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>

		<!-- Charge value -->
		<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
			<input class="mdl-textfield__input" type="text" id="charge_value" name="charge_value" value="<?= self::$v['values']['charge_value'] ?? '' ?>" data-mask="money">
			<label class="mdl-textfield__label" for="charge_value">Valor da cobrança</label>
		</div>

		<!-- Description -->
		<div class="app__box__form__full-width mdl-textfield mdl-js-textfield">
			<textarea class="mdl-textfield__input" type="text" id="description" name="description" maxlength="1000"><?= self::$v['values']['description'] ?? '' ?></textarea>
			<label class="mdl-textfield__label" for="description">Digite uma descrição completa...</label>
		</div>

		<!-- Send button -->
		<div class="app__box__form__full-width">
			<button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
				<?= self::$v['send_button_label'] ?? 'Salvar' ?>
			</button>
		</div>
	</form>
</div>

<script>
	/**
	 * Form validation
	 */
	document.querySelector('#form_charge').addEventListener('submit', function(event) {
		// Serialize the form
		const formData = new FormData(this);

		// Verify if customer_name is not empty
		if (formData.get('customer_name').length === 0) {
			popMessage('Preencha o nome do cliente');
			document.querySelector('#customer_name').focus();

			event.preventDefault();
			return false;
		}

		// Verify if there's a valid cpf (optional)
		if (formData.get('customer_cpf').length > 0 && !validateCpf(formData.get('customer_cpf'))) {
			popMessage('O CPF não é válido');
			document.querySelector('#customer_cpf').focus();

			event.preventDefault();
			return false;
		}

		// Verify if there's a valid e-mail (optional)
		if (formData.get('customer_email').length > 0 && !validateEmail(formData.get('customer_email'))) {
			popMessage('O e-mail digitado não é válido');
			document.querySelector('#customer_email').focus();

			event.preventDefault();
			return false;
		}

		// Verify if value is not empty
		if (formData.get('charge_value').length === 0) {
			popMessage('Preencha o valor da cobrança');
			document.querySelector('#charge_value').focus();

			event.preventDefault();
			return false;

			// Verify if value is higher than 5
		} else if (getAsFloat(formData.get('charge_value')) < 5) {
			popMessage('O valor da cobrança deve ser maior do que R$5,00');
			document.querySelector('#charge_value').focus();

			event.preventDefault();
			return false;
		}

		// Verify if payment_method is not empty
		if (formData.get('payment_methods').length === 0) {
			popMessage('Selecione uma forma de pagamento');
			document.querySelector('#payment_methods').focus();

			event.preventDefault();
			return false;
		}

		// Verify if due_date is not empty
		if (formData.get('due_date').length === 0) {
			popMessage('Preencha uma data de vencimento');
			document.querySelector('#due_date').focus();

			event.preventDefault();
			return false;

			// Verify if due_date is higher than today
		} else if (new Date() > getAsJsDate(formData.get('due_date'))) {
			popMessage('A data de vencimento já passou');
			document.querySelector('#due_date').focus();

			event.preventDefault();
			return false;
		}

		// Verify if description is not empty
		if (formData.get('description').length === 0) {
			popMessage('Preencha a descrição');
			document.querySelector('#description').focus();

			event.preventDefault();
			return false;
		}

		return true;
	})
</script>