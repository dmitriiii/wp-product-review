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

$price_raw = $review_object->get_price_raw();

$pros = $review_object->get_pros();
$cons = $review_object->get_cons();

?>
<div id="wppr-review-<?php echo $review_object->get_ID(); ?>" class="wppr-template wppr-template-default <?php echo is_rtl() ? 'rtl' : ''; ?> wppr-review-container <?php echo (empty($pros) ? 'wppr-review-no-pros' : ''); ?> <?php echo (empty($cons) ? 'wppr-review-no-cons' : ''); ?>">
	<section id="review-statistics" class="article-section">
		<div class="review-wrap-up">
			<div class="cwpr-review-top">
				<div class="cwpr-score-col cwpr-score-container">
					<div class="cwpr-score-wrapper">
						<h2 class="cwpr-score-title"><?php echo esc_html($review_object->get_name()); ?></h2>
						<div class="cwpr-score-value">100</div>
					</div>
					<div class="cwpr-score-level">
						Trust-Level:
						<br>
						"Lorem ipsum"
					</div>
				</div>
				<div class="cwpr-score-col">
					<div class="cwpr-score-review">
						Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perspiciatis omnis at similique repellendus doloremque reiciendis aliquid delectus eius laborum pariatur minima, dolor facere possimus atque cupiditate eum rem sapiente maxime.
					</div>
				</div>
			</div><!-- end .cwpr-review-top -->
			<div class="review-wu-content">
				<div class="review-wu-left">
					<div class="review-wu-left-top">
						<div class="rev-wu-image">
							<?php wppr_layout_get_image($review_object, 'wppr-default-img', 'photo photo-wrapup wppr-product-image'); ?>
						</div>

						<?php wppr_layout_get_rating($review_object, 'donut', 'default', array('review-wu-grade')); ?>
					</div><!-- end .review-wu-left-top -->

					<?php wppr_layout_get_options_ratings($review_object, 'dashes'); ?>

				</div><!-- end .review-wu-left -->

				<div class="review-wu-right">
					<?php wppr_layout_get_pros($review_object, '', 'h2', ''); ?>
					<?php wppr_layout_get_cons($review_object, '', 'h2', ''); ?>
				</div><!-- end .review-wu-right -->

			</div><!-- end .review-wu-content -->
			<div class="cwp-footer">
				<div class="cwpr-footer-col cwpr-price-container">
					<div class="cwpr-price-desc">Lorem ipsum dolor sit amet consectetur, adipisicing elit.</div>
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
					<div class="cwp-affilate-btn-title">Lorem ipsum dolor sit amet consectetur adipisicing elit.</div>
					<?php wppr_layout_get_affiliate_buttons($review_object); ?>
				</div>
			</div>
		</div><!-- end .review-wrap-up -->
	</section>


</div>