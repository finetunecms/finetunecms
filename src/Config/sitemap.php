<?php

return [
	'use_cache'			=> 	false,
	'cache_key'			=> 	'finetune-sitemap.' . config('finetune.url'),
	'cache_duration'	=> 	3600,
	'escaping'			=> 	true,
	'use_limit_size'	=> 	false,
	'max_size'			=> 	null,
	'use_styles'		=> 	true,
	'styles_location'	=> 	null,
];