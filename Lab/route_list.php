<?php

$router->get('/','GalleryController@index');
$router->post('/', 'GalleryController@uploadPhoto');
$router->get('photo/{id}', 'PhotoController@showPhoto');
$router->get('photo-edit/{id}', 'PhotoController@editPhoto');
$router->post('photo-edit/{id}', 'PhotoController@savePhoto');
$router->get('photo-delete/{id}', 'PhotoController@deletePhoto');