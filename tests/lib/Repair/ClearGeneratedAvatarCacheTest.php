<?php
/**
 * @copyright Copyright (c) 2018 Julius Härtl <jus@bitgrid.net>
 *
 * @author Julius Härtl <jus@bitgrid.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Test\Repair;

use OCP\IConfig;
use OCP\Migration\IOutput;
use OC\AvatarManager;
use OC\Repair\ClearGeneratedAvatarCache;

class ClearGeneratedAvatarCacheTest extends \Test\TestCase {

	/** @var AvatarManager */
	private $avatarManager;

	/** @var IOutput */
	private $outputMock;

	/** @var IConfig */
	private $config;

	/** @var ClearGeneratedAvatarCache */
	protected $repair;

	protected function setUp() {
		parent::setUp();

		$this->outputMock    = $this->createMock(IOutput::class);
		$this->avatarManager = $this->createMock(AvatarManager::class);
		$this->config        = $this->createMock(IConfig::class);

		$this->repair = new ClearGeneratedAvatarCache($this->config, $this->avatarManager);
	}

	public function dataVersions() {
		return [
			['0.0.0.0', '15.0.1.2', true],
			['10.0.0.0', '10.0.1.2', false],
			['0.1.0', '0.0.1.2', false],
			['15.0.0.0', '15.0.1.2', false],
			['14.0.0.5', '15.0.0.2', true]
		];
	}

	/**
	 * @dataProvider dataVersions
	 */
	public function testRun($fromVersion, $toVersion, $expected) {
		$this->config->expects($this->once())
		     ->method('getSystemValue')
		     ->with('version', '0.0.0.0')
		     ->willReturn($fromVersion);

		$this->assertEquals($expected, $this->invokePrivate($this->repair, 'shouldRun', [explode('.', $toVersion)]));
	}
}
