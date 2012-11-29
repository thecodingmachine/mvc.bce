<?php
// Controller declaration
MoufManager::getMoufManager()->declareComponent('bceadmin', 'Mouf\\MVC\BCE\\controllers\\BceConfigController', true);
MoufManager::getMoufManager()->bindComponents('bceadmin', 'content', 'block.content');
MoufManager::getMoufManager()->bindComponents('bceadmin', 'template', 'moufTemplate');
?>