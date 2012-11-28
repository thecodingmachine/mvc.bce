<?php
// Controller declaration
MoufManager::getMoufManager()->declareComponent('bceadmin', 'BceConfigController', true);
MoufManager::getMoufManager()->bindComponents('bceadmin', 'template', 'moufTemplate');
?>