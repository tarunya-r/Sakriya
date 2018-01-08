<?php if ( is_active_sidebar( 'giga-store-footer-area' ) ) { ?>
	<div class="footer-widgets"> 
		<div class="container">		
			<div id="content-footer-section" class="row clearfix">
				<?php dynamic_sidebar( 'giga-store-footer-area' ) ?>
			</div>
		</div>
	</div>	
<?php } ?>
<footer id="colophon" class="rsrc-footer" role="contentinfo">
	<div class="container">  
		<div class="row rsrc-author-credits">
			<?php if ( get_theme_mod( 'giga_store_socials', 0 ) == 1 ) : ?>
				<div class="footer-socials text-center">
					<?php
					if ( get_theme_mod( 'giga_store_socials', 0 ) == 1 ) {
						giga_store_social_links();
					}
					?>                 
				</div>
			<?php endif; ?>
			<p class="text-center">
				<?php printf( __( 'Proudly powered by %s', 'giga-store' ), '<a href="' . esc_url( __( 'https://wordpress.org/', 'giga-store' ) ) . '">WordPress</a>' ); ?>
				<span class="sep"> | </span>
				<?php printf( __( 'Theme: %1$s by %2$s', 'giga-store' ), '<a href="http://themes4wp.com/theme/giga-store" title="' . esc_attr__( 'Free WooCommerce WordPress Theme', 'giga-store' ) . '">Giga Store</a>', 'Themes4WP' ); ?>
			</p> 
		</div>
	</div>  
		<div class="container">
	    <div class="row">
	        <div class="col-sm-3">
	            <h4 class="foth4">
	                CONTACT INFORMATION
	                
	            </h4>
	            <p class="fotp1">
	                <i class="fa fa-map-marker" aria-hidden="true"></i>
	                <span class="sp1">
	                    ADDRESS:
	                    
	                </span>
	                </p>
	                 <p class="fotp2">123 BTM Layout, Bangalore, India</p>
	            <p class="fotp1">
	                <i class="fa fa-phone" aria-hidden="true"></i>
	                 <span class="sp1">
	                    PHONE:
	                </span>
	               </p>
	               <p class="fotp2">(123) 456-7890</p>
	            <p class="fotp1">
	                <i class="fa fa-envelope-o" aria-hidden="true"></i>
	                <span class="sp1">
	                    EMAIL:
	                </span>
	               </p>
	                <p class="fotp2">Sakriya@gmail.com</p>
	            
	        </div>
	        <div class="col-sm-3">
	            <h4 class="foth4">
	                BE THE FIRST TO KNOW
	                
	            </h4>
	             <p class="fotp2">Get all the latest information on Events, Sales and Offers.
Sign up for newsletter today.</p>
</div>
         <div class="col-sm-3">
         <h4 class="foth4">
	               MY ACCOUNT
	                
	            </h4>
	            <a href="#" class="fota1">About us</a></br>
	            <a href="#" class="fota1">Contact us</a></br>
	            <a href="#" class="fota1">My Account</a></br>
	            <a href="#" class="fota1">Order history</a></br>
	            <a href="#" class="fota1">Advanced search</a></br>
	            <a href="#" class="fota1">Login</a></br>
	                
	            </div>
	            
	        <div class="col-sm-3">
	            <h4 class="foth4">
	       MAIN FEATURES
	                
	            </h4>
	            
	             <p class="fotp2">Contact Online</p>
	             <p class="fotp2">Pincode Check</p>
	             <p class="fotp2">Free Shipping</p>
	             <p class="fotp2">100% purchase protection</p>
	                 
	                 
	                 
	                 
	            
	            
	        </div>
	       
	    </div>
	    <div class="row">
	        <div class="col-sm-8">
	            <h4 class="fot2h4">
	       PAY USING
	                
	            </h4>
	            <div class="col-sm-2">
	                <img class="img-responsive" src="http://173.199.185.100/~sosasap/tarunya/sports/wp-content/themes/giga-store/img/img1.jpg" alt="sakriya">
	                
	            </div>
	            <div class="col-sm-2">
	                <img class="img-responsive" src="http://173.199.185.100/~sosasap/tarunya/sports/wp-content/themes/giga-store/img/img2.jpg" alt="sakriya">
	                
	            </div>
	            <div class="col-sm-2">
	                <img class="img-responsive" src="http://173.199.185.100/~sosasap/tarunya/sports/wp-content/themes/giga-store/img/img3.jpg" alt="sakriya">
	                
	            </div>
	            <div class="col-sm-2">
	                <img class="img-responsive" src="http://173.199.185.100/~sosasap/tarunya/sports/wp-content/themes/giga-store/img/img5.jpg" alt="sakriya">
	                
	            </div>
	            <div class="col-sm-2">
	                <img class="img-responsive" src="http://173.199.185.100/~sosasap/tarunya/sports/wp-content/themes/giga-store/img/imag5.jpg" alt="sakriya">
	                
	            </div>
	            <div class="col-sm-2">
	                <img class="img-responsive" src="http://173.199.185.100/~sosasap/tarunya/sports/wp-content/themes/giga-store/img/imag6.png" alt="sakriya">
	                
	            </div>
	            
	        </div>
	    </div>
	    
	</div>
	
	
	
	
</footer> 

<p id="back-top">
	<a href="#top"><span></span></a>
</p>
<!-- end main container -->
<nav id="menu" class="off-canvas-menu">
	<?php
	wp_nav_menu( array(
		'theme_location' => 'main_menu',
		'container'		 => false,
	) );
	?>
</nav>
<?php wp_footer(); ?>
</body>
</html>
