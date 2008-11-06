<?php
Library::import('recess.lang.Annotation');

abstract class ModelAnnotation extends Annotation {
	abstract function massage(ModelDescriptor &$descriptor);
	
	final protected function massageRelationshipHelper(ModelDescriptor &$descriptor, Relationship $relationship) {
		$relationship->init($descriptor->modelClass, $this->relationshipName);
		
		foreach($this->settings as $key => $value) {
			switch(strtolower($key)) {
				case Relationship::FOREIGN_KEY:
					$relationship->foreignKey = $value;
					break;
				case Relationship::FOREIGN_CLASS:
					$relationship->foreignClass = $value;
					break;
				case Relationship::THROUGH:
					$relationship->joinTable = $value;
					break;
				case Relationship::ON_DELETE:
					$lowerValue = strtolower($value);
					switch(strtolower($lowerValue)) {
						case Relationship::CASCADE:
						case Relationship::DELETE:
						case Relationship::NULLIFY:
							break;
						default:
							throw new RecessException('Invalid OnDelete setting: "' . $value . '".', get_defined_vars());
					}
					$relationship->onDelete = strtolower($lowerValue);
					break;
			}
		}

		$descriptor->relationships[$relationship->name] = $relationship;
		
		$relationship->attachMethodsToModelDescriptor($descriptor);
	}
}
?>