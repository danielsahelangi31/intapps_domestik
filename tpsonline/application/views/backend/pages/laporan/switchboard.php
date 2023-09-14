<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Pusat Laporan</h1>
			<p class="lead">
				<small>Pilih kategori laporan, sub kategori laporan dan isi parameter</small>
			</p>
			
			<?php echo form_open('laporan/execute_form', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal', 'target' => '_blank')) ?>
	
			<div class="row">
				<div class="col-md-4">
					<h4>1. Pilih Kategori Laporan</h4>
					<select id="category" name="cat_id" size="10" class="form-control" style="height:200px">
						<?php
						foreach($root_cat as $cat_id => $label){
						?>
						<option value="<?php echo $cat_id ?>"><?php echo $label ?></option>
						<?php
						}
						?>
					</select>
				</div>
				<div class="col-md-4" id="handle_sub_category">
					<h4>2. Pilih Sub Kategori Laporan</h4>
					<select id="sub_category" name="sub_cat_id" size="10" class="form-control" style="height:200px">
						
					</select>
				</div>
				<div class="col-md-4" id="handle_report_form_landing">
					<h4>3. Isi Parameter Laporan</h4>
					<div id="report_form_landing">
						<p><em>Pilihlah Kategori Laporan dan Sub Kategori Laporan untuk menampilkan pilih suai laporan</em></p>
					</div>
				</div>
			</div>
			<div class="row visible-xs-block visible-sm-block">
				<div class="col-md-12">
					<div class="pull-right">
						<button id="scroll_top" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-arrow-up"></span> Scroll Top</button>
					</div>
				</div>
			</div>
			
			<?php echo form_close() ?>
		
			<div id="kampret_loader"></div>
		</div><!-- /.container -->
		
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
	
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.scrollTo-1.4.3.1-min.js') ?>"></script>
	
	<script type="text/javascript">
	/* on AJAX Load Animator Overlay
	 */
	function LoadingAnimator(target, delay){
		this.target = $(target);
		this.delay = typeof(delay) !== 'undefined' ? delay : 0;
		
		this.anim_container = null;
		this.anim_viewport = null;
		this.timer_id = null;
		this.target_fps = 60;
				
		this.sprite_movement = 40;
		this.sprite_edge = -3000;
		this.sprite_location = bs.baseURL + '/assets/css/img/control/kampret.png';
		this.sprite_vp_width = 40;
		this.sprite_vp_height = 40;
		this.interval = 1000 / this.target_fps;
	}
	
	LoadingAnimator.prototype.animate = function(){
		var target = this.anim_viewport;
		
		var target_left = 0;
		
		var last_pos = $(target).css('background-position');
		var last_pos_left = last_pos.split(' ')[0].replace('px', '');
		
		if(last_pos_left - this.sprite_movement <= this.sprite_edge){
			target_left = 0;
		}else{
			target_left = parseInt(last_pos_left) - this.sprite_movement;
		}
		
		$(target).css('background-position', target_left + 'px 0px');
	}	
	
	LoadingAnimator.prototype.start = function(){
		if(this.delay){
			me = this;
			this.timer_id = setTimeout(function(){
				me.initialize();
			}, this.delay);
		}else{
			this.initialize();
		}
	}
	
	LoadingAnimator.prototype.initialize = function(){
		console.log("initialized");
		
		if(this.timer_id !== null){
			this.stop();
		}
	
		var target = this.target;
		
		// Make Container
		this.anim_container = $('<div/>').css({
			display : 'none',
			position : 'absolute',
			width : $(target).outerWidth(),
			height : $(target).outerHeight(),
			top : $(target).offset().top,
			left : $(target).offset().left,
			background : 'rgba(0,0,0,.4)'
		});
	
		// Make Viewport
		this.anim_viewport = $('<div/>').css({
			'margin' : 'auto',
			'position' : 'relative',
			'background-image' : "url('" + this.sprite_location + "')",
			'background-repeat' : 'no-repeat',
			'background-position' : '0px 0px',
			'width' : this.sprite_vp_width + 'px',
			'height' : this.sprite_vp_height + 'px',
			'top' : Math.round($(target).outerHeight() / 2) - this.sprite_vp_height + 'px',
		});
		
		// Append To Body
		$(this.anim_container).append(this.anim_viewport).appendTo('body').fadeIn(200);
		
		// The 'this' reference not the 'this' we looking for on callback
		// so we renaming it
		var me = this;
		this.timer_id = setInterval(function(){
			me.animate();
		}, this.interval);
	}
	
	LoadingAnimator.prototype.stop = function(){
		if(this.timer_id !== null){
			clearInterval(this.timer_id);
		}
		
		this.timer_id = null;
		$(this.anim_container).fadeOut(200, function(){
			$(this).remove();
		});
	}
	
	$(document).ready(function(){
		$('#category').change(function(){
			if($(this).val() !== ''){
				var url = bs.baseURL + 'laporan/get_sub_cat/' + bs.token;
				var param = {
					'cat_id' : $(this).val()
				};
				
				var overlay = new LoadingAnimator(this, 250);
				overlay.start();
				
				$.post(url, param, function(data){
					if(data.success){
						$('#sub_category').html('');
						
						for(var sub_cat_id in data.sub_cat){
							var elStr = '<option value="' + sub_cat_id + '">' + data.sub_cat[sub_cat_id] + '</option>';
							$(elStr).appendTo('#sub_category');
						}
						
						$.scrollTo($('#handle_sub_category'), 400, {offset: 0 - $('.navbar').outerHeight() - 10});
					}
					
					overlay.stop();
				}, 'json');
			}
		});
		
		$('#sub_category').change(function(){
			var url = bs.baseURL + 'laporan/get_form/' + bs.token;
			var param = {
				'cat_id' : $('#category').val(),
				'sub_cat_id' : $(this).val()
			};
				
			var overlay = new LoadingAnimator(this, 250);
			overlay.start();
			
			$.post(url, param, function(data){
				$('#report_form_landing').fadeOut(200, function(){
					$('#report_form_landing').html(data);
					$('#report_form_landing .date').datepicker(datepickerBase);
					
					$('#report_form_landing').fadeIn(200, function(){
						$.scrollTo('#handle_report_form_landing', 400, {offset: 0 - $('.navbar').outerHeight() - 20});
					});
				});
				
				overlay.stop();
			});
		});
		
		$('#scroll_top').click(function(){
			$.scrollTo('#wrap', 400);
		});
		
		// Invoke on change in root cat to prevent blank list
		$('#category').change();
	});
	</script>
</body>
</html>