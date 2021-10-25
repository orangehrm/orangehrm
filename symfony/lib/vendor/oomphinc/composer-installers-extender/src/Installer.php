<?php

namespace OomphInc\ComposerInstallersExtender;

use Composer\Installer\LibraryInstaller;
use Composer\Installers\Installer as ComposerInstaller;
use Composer\Package\PackageInterface;

class Installer extends ComposerInstaller {

	protected $packageTypes;

	public function getInstallPath( PackageInterface $package ) {
		$installer = new InstallerHelper( $package, $this->composer, $this->io );
		$path = $installer->getInstallPath( $package, $package->getType() );
		// if the path is false, use the default installer path instead
		return $path !== false ? $path : LibraryInstaller::getInstallPath( $package );
	}

	public function supports( $packageType ) {
		// grab the package types once
		if ( !isset( $this->packageTypes ) ) {
			$this->packageTypes = false;
			if ( $this->composer->getPackage() ) {
				// get data from the 'extra' field
				$extra = $this->composer->getPackage()->getExtra();
				if ( !empty( $extra['installer-types'] ) ) {
					$this->packageTypes = (array) $extra['installer-types'];
				}
			}
		}
		return is_array( $this->packageTypes ) && in_array( $packageType, $this->packageTypes );
	}

}