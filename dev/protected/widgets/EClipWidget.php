<?php
class EClipWidget extends CClipWidget {
	public function run () {
		$clip = ob_get_clean();
		if ( $this->renderClip ) {
			echo $clip;
		}
		if ( !empty($this->getController()->clips[$this->getId()]) ) {
			$this->getController()->clips[$this->getId()] .= $clip;
		}
		else {
			$this->getController()->getClips()->add($this->getId(), $clip);
		}
	}
}