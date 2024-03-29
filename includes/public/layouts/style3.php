<?php

/**
 *  WPPR front page layout.
 *
 * @package     WPPR
 * @subpackage  Layouts
 * @global      WPPR_Review_Model $review_object The inherited review object.
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

//$review_object->enable_third_party();
$price_raw = $review_object->get_price_raw();
$pros = $review_object->get_pros();
$cons = $review_object->get_cons();
$ranges = get_field('trust_level_ranges', 'option') ? get_field('trust_level_ranges', 'option') : [];

$score = get_field('k8_acf_trust_level', $review_object->get_ID()) ? (int) get_field('k8_acf_trust_level', $review_object->get_ID()) : 0;
$range = array_filter($ranges, function ($range) use ($score) {
	return  $score >= $range['range_start'] && $range['range_end'] >= $score;
});

$show_trust_lvl_detail = get_field('show_trust_level_detail', 'option');

$prep_range = !empty($range) ? array_values($range) : [];
$range = !empty($range) ? array_shift($prep_range) : (!empty($ranges) ? $ranges[0] : [
	'color' => '#a5a5a5',
	'range_name' => 'N/A',
	'range_description' => ''
]);

$total_votes = $review_object->get_third_party_votes();
$desc =  apply_filters(
	'prepare_description',
	get_field('third_party_review_short_desc', $review_object->get_ID()) ?
		get_field('third_party_review_short_desc', $review_object->get_ID()) : (get_field('third_party_review_default_short_desc', 'option') ?
			get_field('third_party_review_default_short_desc', 'option') :
			''),
	[
		'vpn' => esc_html($review_object->get_name()),
		'detail' => "<a href=\"#subs-modal-{$review_object->get_ID()}\" rel=\"nofollow\">$1</a>"
	]
);


?>
<div id="wppr-review-<?php echo $review_object->get_ID(); ?>" data-vpn-id="<?= get_field('k8_acf_vpnid', $review_object->get_ID()) ?>" class="wppr-template wppr-template-default <?php echo is_rtl() ? 'rtl' : ''; ?> wppr-review-container <?php echo (empty($pros) ? 'wppr-review-no-pros' : ''); ?> <?php echo (empty($cons) ? 'wppr-review-no-cons' : ''); ?>">
	<section id="review-trust-lvl" class="article-section">
		<div class="review-wrap-up">
			<div class="cwpr-review-top">
				<div class="cwpr-score-col cwpr-score-container" <? if ($range['color']) { ?>style="background-color: <?= $range['color'] ?>" <? } ?>>
					<div class="cwpr-score-wrapper">
						<h2 class="cwpr-score-title"><?php echo esc_html($review_object->get_name()); ?></h2>
						<div class="cwpr-score-value"><?= $score ?><? if ($show_trust_lvl_detail) { ?><sup><a href="#subs-modal-<?= $review_object->get_ID() ?>" rel="nofollow">?</a></sup><? } ?></div>
					</div>
					<div class="cwpr-score-level">
						<?= get_field('trust_level_title', 'option') ?>
						<br>
						<?= $range['range_name'] ?>
					</div>
				</div>
				<? if ($range['range_description']) { ?>
					<div class="cwpr-score-col">
						<div class="cwpr-score-review">
							<?= apply_filters('prepare_description', $range['range_description'], [
								'vpn' => esc_html($review_object->get_name()),
								'detail' => "<a href=\"#subs-modal-{$review_object->get_ID()}\" rel=\"nofollow\">$1</a>"
							]) ?>
						</div>
					</div>
				<? } ?>
			</div><!-- end .cwpr-review-top -->
			<div class="review-wu-content">
				<div class="review-wu-left">
					<div class="review-wu-left-top">
						<div class="rev-wu-image">
							<?php wppr_layout_get_image($review_object, 'wppr-default-img', 'photo photo-wrapup wppr-product-image'); ?>
						</div>

						<?php wppr_layout_get_rating($review_object, 'donut', 'default', array('review-wu-grade'), false); ?>
					</div><!-- end .review-wu-left-top -->
					<? if ($total_votes != -1) { ?>
						<div class="review-wu-left-mid">
							<a class="review-wu-reviews-link" href="#reviews-detail" rel="nofollow">
								<? wppr_display_rating_stars(null, $review_object, false, true) ?>
								<span class="review-wu-reviews-count">
									<?= __('Reviews', 'wp-product-review') ?> (<?= number_format($total_votes) ?>)
								</span>
							</a>
						</div>
					<? } ?>
					<?php wppr_layout_get_privacy_info($review_object); ?>	
					<?php wppr_layout_get_options_ratings($review_object, 'dashes'); ?>

				</div><!-- end .review-wu-left -->

				<div class="review-wu-right">
					<?php wppr_layout_get_pros($review_object, 'h2', '', ''); ?>
					<?php wppr_layout_get_cons($review_object, 'h2', '', ''); ?>
				</div><!-- end .review-wu-right -->

			</div><!-- end .review-wu-content -->
			<div class="cwp-footer">
				<div class="cwpr-footer-col cwpr-price-container">
					<? if (get_field('trust_level_text_before_price', 'option')) { ?>
						<div class="cwpr-price-desc">
							<?= apply_filters(
								'prepare_description',
								get_field('trust_level_text_before_price', 'option'),
								[
									'vpn' => esc_html($review_object->get_name()),
									'detail' => "<a href=\"#subs-modal-{$review_object->get_ID()}\" rel=\"nofollow\">$1</a>"
								]
							)
							?></div>
					<? } ?>
					<span class="cwp-item-price cwp-item">
						<?php echo esc_html(empty($price_raw) ? '' : $price_raw); ?>
						<em><?php
							if (get_field('k8_acf_vpndet_curr', $review_object->get_ID())) {
								echo get_field('k8_acf_vpndet_curr', $review_object->get_ID())['label'];
							}
							?></em>
					</span>
				</div>
				<div class="cwpr-footer-col">
					<div class="cwp-affilate-btn-title">
						<?= apply_filters(
							'prepare_description',
							get_field('trust_level_text_before_link', 'option'),
							[
								'vpn' => esc_html($review_object->get_name()),
								'detail' => "<a href=\"#subs-modal-{$review_object->get_ID()}\" rel=\"nofollow\">$1</a>"
							]
						)
						?>
					</div>
					<?php wppr_layout_get_affiliate_buttons($review_object); ?>
				</div>
			</div>
		</div><!-- end .review-wrap-up -->
	</section>
	<? if ($total_votes != -1) { ?>
		<div id="reviews-detail" class="wu-modal" style="display: none;">
			<div class="wu-modal__loading">
				<img alt="loading..." src="<?php echo WPPR_URL; ?>/assets/img/loading.svg">
			</div>
			<div class="wu-modal__inner" style="display: none; max-width: 620px">
				<div role="button" class="wu-modal__close" title="close">
				</div>
				<div class="wu-modal__content">
					<div class="wu-modal__title">
						<?= ucfirst(__('reviews', 'wp-product-review')) ?>
					</div>
					<? if ($desc) { ?>
						<p class="wu-modal__subtitle"><?= $desc ?></p>
					<? } ?>
					<div class="wu-modal__results">
					</div>
				</div>
			</div>
		</div>
	<? } ?>
	<? if ($show_trust_lvl_detail) { ?>
		<div id="subs-modal-<?= $review_object->get_ID() ?>" class="wu-modal" style="display: none">
			<div class="wu-modal__loading">
				<img alt="loading..." src="<?php echo WPPR_URL; ?>/assets/img/loading.svg">
			</div>
			<div class="wu-modal__inner" style="display: none">
				<div role="button" class="wu-modal__close" title="close">
				</div>
				<div class="wu-modal__content">
					<div class="wu-modal__title">
						<img class="wu-modal__logo" src="<?php echo WPPR_URL; ?>/assets/img/wu_logo.png" alt="">VPN Trust-Level
					</div>
					<p class="wu-modal__subtitle"><strong>Fragen werden in der Originalsprache veröffentlicht in der diese gestellt wurden, um Manipulation durch Interpretation zu vermeiden.</strong></p>
					<div class="wu-modal__results">
					</div>
				</div>
			</div>
		</div>
	<? } ?>
</div>