<?php
/*
 *  $Id: Migrate.php 2761 2007-10-07 23:42:29Z zYne $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

/**
 * Doctrine_Task_Migrate
 *
 * @package     Doctrine
 * @subpackage  Task
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision: 2761 $
 * @author      Jonathan H. Wage <jwage@mac.com>
 */
class Doctrine_Task_Migrate extends Doctrine_Task
{
    public $description          =   'Migrate database to latest version or the specified version',
           $requiredArguments    =   array('migrations_path' => 'Specify path to your migrations directory.'),
           $optionalArguments    =   array('timestamp' => 'Timestamp to migrate to. If you do not specify, the db will be migrated from the current version to the latest.  Provide \'-1\' to list past migrations.');
    
    public function execute()
    {
        if($this->getArgument('timestamp') == -1) {
            $conn = Doctrine_Manager::connection();
            $m = new Doctrine_Migration();
            $existingMigrations = $conn->fetchAll("SELECT timestamp_value, class_name FROM " . $m->getTableName() . " ORDER BY timestamp_value ASC");
            if(empty($existingMigrations)) {
                $this->notify('No migrations have occurred yet.');
            } else {
                $output = "";
                foreach($existingMigrations as $migration) {
                    $output .= "\n{$migration['timestamp_value']} - {$migration['class_name']}";
                }
                $this->notify($output);
            }
        } else {
            try {
                $version = Doctrine_Core::migrate($this->getArgument('migrations_path'), $this->getArgument('timestamp'));
            } catch (Doctrine_migration_Exception $dme) {
                if (strpos($dme->getMessage(), 'No new migrations to run')) {
                    $this->notify('No new migrations to run');

                    return;
                } else {
                    throw $dme;
                }
            }
        
            $this->notify('migrated successfully');
        }
    }
}
