<?php
/**
 * Login Form
 *
 * @package Adorkini
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class="container mx-auto px-4 py-12 max-w-4xl" id="customer_login">

	<div class="grid grid-cols-1 md:grid-cols-2 gap-12">

        <!-- Login Column -->
		<div class="u-column1 col-1">

			<h2 class="text-2xl font-bold mb-6 text-gray-900"><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>

			<form class="woocommerce-form woocommerce-form-login login space-y-4" method="post">

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<div>
					<label for="username" class="block text-sm font-medium text-gray-700 mb-1"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
				</div>

				<div>
					<label for="password" class="block text-sm font-medium text-gray-700 mb-1"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" type="password" name="password" id="password" autocomplete="current-password" />
				</div>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<div class="flex items-center justify-between">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox inline-flex items-center">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox form-checkbox h-4 w-4 text-primary rounded border-gray-300" name="rememberme" type="checkbox" id="rememberme" value="forever" /> 
                        <span class="ml-2 text-sm text-gray-600"><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
					</label>
                    
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-sm text-primary hover:underline"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
				</div>

                <div class="pt-2">
                    <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                    <button type="submit" class="woocommerce-button button woocommerce-form-login__submit w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-blue-600 transition-colors shadow-md" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
                </div>

				<?php do_action( 'woocommerce_login_form_end' ); ?>

			</form>

		</div>

        <!-- Register Column -->
		<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

		<div class="u-column2 col-2 border-t md:border-t-0 md:border-l border-gray-200 pt-12 md:pt-0 md:pl-12">

			<h2 class="text-2xl font-bold mb-6 text-gray-900"><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>

			<form method="post" class="woocommerce-form woocommerce-form-register register space-y-4" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

					<div>
						<label for="reg_username" class="block text-sm font-medium text-gray-700 mb-1"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
					</div>

				<?php endif; ?>

				<div>
					<label for="reg_email" class="block text-sm font-medium text-gray-700 mb-1"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="email" class="woocommerce-Input woocommerce-Input--text input-text w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
				</div>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

					<div>
						<label for="reg_password" class="block text-sm font-medium text-gray-700 mb-1"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
						<input class="woocommerce-Input woocommerce-Input--text input-text w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" type="password" name="password" id="reg_password" autocomplete="new-password" />
					</div>

				<?php else : ?>

					<p class="text-sm text-gray-600 mb-4"><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>

				<?php endif; ?>

				<?php do_action( 'woocommerce_register_form' ); ?>

				<div class="pt-2">
					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit w-full bg-gray-900 text-white font-bold py-3 rounded-lg hover:bg-gray-800 transition-colors shadow-md" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
				</div>

				<?php do_action( 'woocommerce_register_form_end' ); ?>

			</form>

		</div>

		<?php endif; ?>

	</div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
