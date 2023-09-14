			<ul class="pagination pull-right">
            	<?php
				if($cfg->currPage == 1){
				?>
				<li class="disabled"><a href="#">&laquo;</a></li>
                <?php
				}else{
				?>
				<li><a href="<?php echo site_url($cfg->pagingURL)."/p:".($cfg->currPage - 1) ?>">&laquo;</a></li>
                <?php
				}
				
				$start = ($cfg->currPage - 5) < 1 ? 1 : ($cfg->currPage - 5);
				$end = $start + 9;
				
				for($i = $start; $i < $end && $i <= $cfg->totalPage; $i++){
					if($i == $cfg->currPage){
				?>	
                <li class="active"><a href="#" class="active"><?php echo $i ?></a></li>
				<?php
					}else{
				?>
                <li><a href="<?php echo site_url($cfg->pagingURL)."/p:$i" ?>"><?php echo $i ?></a></li>
				<?php
					}	
				}
				
				if($cfg->currPage < $cfg->totalPage){
				?>
                <li><a href="<?php echo site_url($cfg->pagingURL)."/p:".($cfg->currPage + 1) ?>">&raquo;</a></li>
				<?php
				}else{
				?>	
				<li class="disabled"><a href="#">&raquo;</a></li>
				<?php
				}
				?>
			</ul>