<?php
    class CompetitionSale extends AppModel 
	{
    	var $name = 'CompetitionSale';
    	var $useTable = 'competition_sales';

    	var $belongsTo = array(
			'CompetitionExtension' => array(
				'className' => 'Competitions.CompetitionExtension',
				'foreignKey' => false,
				'conditions' => array('CompetitionSale.id_extension = CompetitionExtension.id_extension'),
			),
			'Competition' => array(
				'className' => 'Competitions.Competition',
				'foreignKey' => false,
				'conditions' => array('CompetitionSale.id_competition = Competition.id'),
			)
		);
	}
?>