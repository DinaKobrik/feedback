<?php

	function feedback_theme_scripts() {
		wp_enqueue_style('feedback-style', get_stylesheet_uri());
		wp_enqueue_script('feedback-js', get_template_directory_uri() . '/js/feedback.js', [], null, true);
	}
	add_action('wp_enqueue_scripts', 'feedback_theme_scripts');

	// Форма
	function feedback_form_shortcode() {
		ob_start();
		session_start();
		$errors = isset($_SESSION['feedback_errors']) ? $_SESSION['feedback_errors'] : [];
		$success = isset($_SESSION['feedback_success']) ? $_SESSION['feedback_success'] : '';
		$name_error = isset($errors['name']) ? $errors['name'] : '';
		$email_error = isset($errors['email']) ? $errors['email'] : '';
		$message_error = isset($errors['message']) ? $errors['message'] : '';
		unset($_SESSION['feedback_errors'], $_SESSION['feedback_success']);
		?>
		<form id="feedback-form" class="feedback-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" novalidate>
			<input type="hidden" name="action" value="submit_feedback">
			<div class="feedback-form__field">
				<label class="feedback-form__label" for="feedback_name">Имя:</label>
				<input class="feedback-form__input" type="text" id="feedback_name" name="feedback_name">
				<span class="feedback-form__error" id="feedback_name_error"><?php echo esc_html($name_error); ?></span>
			</div>
			<div class="feedback-form__field">
				<label class="feedback-form__label" for="feedback_email">Email:</label>
				<input class="feedback-form__input" type="email" id="feedback_email" name="feedback_email">
				<span class="feedback-form__error" id="feedback_email_error"><?php echo esc_html($email_error); ?></span>
			</div>
			<div class="feedback-form__field">
				<label class="feedback-form__label" for="feedback_message">Отзыв:</label>
				<textarea class="feedback-form__textarea" id="feedback_message" name="feedback_message"></textarea>
				<span class="feedback-form__error" id="feedback_message_error"><?php echo esc_html($message_error); ?></span>
			</div>
			<?php if ($success) : ?>
				<div class="feedback-form__success"><?php echo esc_html($success); ?></div>
			<?php endif; ?>
			<button class="feedback-form__submit" type="submit">Отправить</button>
		</form>
		<?php
		return ob_get_clean();
	}
	add_shortcode('feedback_form', 'feedback_form_shortcode');

	// Проверка формы
	function handle_feedback_form() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_name'])) {
			$name = sanitize_text_field($_POST['feedback_name']);
			$email = sanitize_email($_POST['feedback_email']);
			$message = wp_kses_post($_POST['feedback_message']);

			// Валидация
			$errors = [];
			if (empty($name)) {
				$errors['name'] = 'Имя не может быть пустым';
			}
			if (!is_email($email)) {
				$errors['email'] = 'Некорректный email';
			}
			if ($message !== strip_tags($message)) {
				$errors['message'] = 'Текст отзыва не должен содержать HTML';
			}

			if (!empty($errors)) {
				session_start();
				$_SESSION['feedback_errors'] = $errors;
				wp_redirect(wp_get_referer());
				exit;
			}

			// Сохранение отзыва
			$post_data = array(
				'post_title'   => $name, 
				'post_content' => $message,
				'post_status'  => 'publish',
				'post_type'    => 'post',
				'post_category' => array(get_cat_ID('Отзывы')),
				'meta_input'   => array(
					'feedback_email' => $email
				)
			);

			$post_id = wp_insert_post($post_data);

			if ($post_id) {
				session_start();
				$_SESSION['feedback_success'] = 'Отзыв успешно отправлен';
				wp_redirect(wp_get_referer());
				exit;
			} else {
				session_start();
				$_SESSION['feedback_errors'] = ['general' => 'Ошибка при сохранении отзыва'];
				wp_redirect(wp_get_referer());
				exit;
			}
		}
	}
	add_action('admin_post_submit_feedback', 'handle_feedback_form');
	add_action('admin_post_nopriv_submit_feedback', 'handle_feedback_form');

	// Отзывы
	function display_recent_reviews() {
		$args = array(
			'post_type' => 'post',
			'cat' => get_cat_ID('Отзывы'),
			'posts_per_page' => 3,
			'post_status' => 'publish'
		);
		$reviews = new WP_Query($args);
		ob_start();
		if ($reviews->have_posts()) :
			echo '<div class="reviews">';
			while ($reviews->have_posts()) : $reviews->the_post();
				$content = get_the_content();
				$content = preg_replace('/<\/?p>/', '', $content);
				?>
				<div class="reviews__item">
					<div class="reviews__title"><?php the_title(); ?></div>
					<div class="reviews__text"><?php echo $content; ?></div>
					<p class="reviews__email"><strong>Email:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'feedback_email', true)); ?></p>
				</div>
				<?php
			endwhile;
			echo '</div>';
			wp_reset_postdata();
		else :
			echo '<p class="reviews__empty">Отзывов пока нет.</p>';
		endif;
		return ob_get_clean();
	}
	add_shortcode('recent_reviews', 'display_recent_reviews');
?>
