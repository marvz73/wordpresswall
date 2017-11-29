<?php

	//Render Shift options form
	$builder->register_form( $form );

	$builder->register_section( $info );

	$builder->register_control( $controls );

	$builder->register_control( $buttons );

	$builder->render();

?>