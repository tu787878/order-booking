<?php
//list comments of product
function dsmart_list_comments($post_id){
	$post_id = get_the_ID();
	global $wpdb;
	$tablename = $wpdb->prefix . "rating_product";
	$data = $wpdb->get_results("SELECT * FROM {$tablename} WHERE post_id = ".$post_id." ORDER BY time DESC");
	if($data){ ?>
		<div class="list-comment-section">
			<h4 class="dsmart-title"><?php _e("List reviews",'dsmart') ?></h4>
			<div class="dsmart-list-comments">
				<?php foreach ($data as $item) {
				$author_id = $item->user_id;
				$comment = $item->comment;
				$rating = intval($item->rating);
				$rating_percent = intval($rating) * 20;
				$time = $item->time;
				$only_date = explode(' ', $time)[0];
				$convert_date = date('d F, o',strtotime($only_date));
				$userdata  = get_userdata($author_id);
				if($userdata->display_name=="")
					$display_name = $userdata->user_login;
				else
					$display_name = $userdata->display_name; ?>
					<div class="item">
						<?php echo get_avatar($author_id, 50);?>
						<div class="wrap">
							<div class="comment-top-info">
								<p class="meta">
									<span><?php echo $display_name; ?></span> - <?php echo $convert_date; ?>
								</p>
								<div class="review-rating">
			                        <div class="review-stars">
			                          <span style="width: <?php echo $rating_percent; ?>%;" class="review-stars-range"></span>
			                        </div>
			                        <span class="rating-total"></span>
			                    </div>
							</div>
							<div class="comment-description"><?php echo $comment; ?></div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php }else{ ?>
		<div class="list-comment-section">
			<div class="no-comment"><?php _e("No review.",'dsmart') ?></div>
		</div>
	<?php }
}
//comment form
function dsmart_comment_form(){
	if(is_user_logged_in()){ ?>
		<div class="comment-form-section">
			<h4 class="dsmart-title"><?php _e("Add new review",'dsmart') ?></h4>
			<div class="comment-notify"></div>
			<form method="POST" action="#" id="comment-form">
				<div class="comment-form-rating">
					<select id="rating-star" name="rating-star">
					  <option value="1">1</option>
					  <option value="2">2</option>
					  <option value="3">3</option>
					  <option value="4">4</option>
					  <option value="5">5</option>
					</select>
				</div>
				<div class="comment-text">
					<textarea class="dsmart-field" rows="6" name="comment-text" placeholder="<?php _e('Comment','dsmart') ?>" required></textarea>
				</div>
				<button type="submit" name="submit-rating" class="dsmart-button"><?php _e("Post review",'dsmart') ?></button>
			</form>
		</div>
	<?php }
}
//comment form submit
function submit_comment_form(){
	if(isset($_POST['submit-rating'])){
		$check = false;
		$status = 1;
		$post_id = get_the_ID();
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		$rating_star = $_POST['rating-star'];
		$comment = $_POST['comment-text'];
		if($rating_star == "" || $comment == ""){
			$status = 3;
		}else{
			$rating_star = intval($rating_star);
			if($rating_star < 0){
				$rating_star = 1;
			}elseif($rating_star > 5){
				$rating_star = 5;
			}
			global $wpdb;
			$time = date('Y-m-d H:i:s');
			$tablename = $wpdb->prefix . "rating_product";
			$wpdb->insert($tablename, array('post_id' => $post_id, 'user_id' => $user_id,'comment' => $comment,'rating' => $rating_star, 'time' => $time));
			$data = $wpdb->get_results("SELECT * FROM {$tablename} WHERE post_id = ".$post_id);
			$total = 0;
			$avg = 0;
			if($data){
				foreach ($data as $item) {
					$rating = intval($item->rating);
					$total = $total + $rating;
				}
				$avg = $total / count($data);
			}
			update_post_meta($post_id,'avg_rating',$avg);
			$status = 2;
		}
		return $status;
	}else{
		return false;
	}
}
//submit comment notify
function submit_comment_notify($notify = null){
	if($notify == 1){ ?>
		<div class="alert alert-danger"><?php _e('An error occurred. Please try again.','dsmart'); ?></div>
	<?php }elseif($notify == 2){ ?>
		<div class="alert alert-success"><?php _e('Add new review successful.','dsmart');?></div>
	<?php }elseif($notify == 3){ ?>
		<div class="alert alert-danger"><?php _e('An error occurred. Please try again.','dsmart'); ?></div>
	<?php }
}