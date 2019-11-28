<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * System role database plugin settings and presets.
 *
 * @package   tool_sysroledatabase
 * @copyright 2019 Michael Vangelovski, Canberra Grammar School <michael.vangelovski@cgs.act.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


if ($hassiteconfig) {

    // Add a new category under tools.
    $ADMIN->add('tools',
        new admin_category('tool_sysroledatabase', get_string('pluginname', 'tool_sysroledatabase')));

    $settings = new admin_settingpage('tool_sysroledatabase_settings', new lang_string('settings', 'tool_sysroledatabase'),
        'moodle/site:config', false);

    // Add the settings page.
    $ADMIN->add('tool_sysroledatabase', $settings);

    // Add the test settings page.
    $ADMIN->add('tool_sysroledatabase',
            new admin_externalpage('tool_sysroledatabase_test', get_string('testsettings', 'tool_sysroledatabase'),
                $CFG->wwwroot . '/' . $CFG->admin . '/tool/catroledatabase/test_settings.php'));

    // General settings.
    $settings->add(new admin_setting_heading('tool_sysroledatabase_settings', '',
        get_string('pluginname_desc', 'tool_sysroledatabase')));

    $settings->add(new admin_setting_heading('tool_sysroledatabase_exdbheader',
        get_string('settingsheaderdb', 'tool_sysroledatabase'), ''));

    $options = array('', "pdo", "pdo_mssql", "pdo_sqlsrv", "access", "ado_access", "ado", "ado_mssql", "borland_ibase",
        "csv", "db2", "fbsql", "firebird", "ibase", "informix72", "informix", "mssql", "mssql_n", "mssqlnative", "mysql",
        "mysqli", "mysqlt", "oci805", "oci8", "oci8po", "odbc", "odbc_mssql", "odbc_oracle", "oracle", "postgres64",
        "postgres7", "postgres", "proxy", "sqlanywhere", "sybase", "vfp");
    $options = array_combine($options, $options);
    $settings->add(new admin_setting_configselect('tool_sysroledatabase/dbtype',
        get_string('dbtype', 'tool_sysroledatabase'),
        get_string('dbtype_desc', 'tool_sysroledatabase'), '', $options));

    $settings->add(new admin_setting_configtext('tool_sysroledatabase/dbhost',
        get_string('dbhost', 'tool_sysroledatabase'),
        get_string('dbhost_desc', 'tool_sysroledatabase'), ''));

    $settings->add(new admin_setting_configtext('tool_sysroledatabase/dbuser',
        get_string('dbuser', 'tool_sysroledatabase'), '', ''));

    $settings->add(new admin_setting_configpasswordunmask('tool_sysroledatabase/dbpass',
        get_string('dbpass', 'tool_sysroledatabase'), '', ''));

    $settings->add(new admin_setting_configtext('tool_sysroledatabase/dbname',
        get_string('dbname', 'tool_sysroledatabase'),
        get_string('dbname_desc', 'tool_sysroledatabase'), ''));

    $settings->add(new admin_setting_configtext('tool_sysroledatabase/dbencoding',
        get_string('dbencoding', 'tool_sysroledatabase'), '', 'utf-8'));

    $settings->add(new admin_setting_configtext('tool_sysroledatabase/dbsetupsql',
        get_string('dbsetupsql', 'tool_sysroledatabase'),
        get_string('dbsetupsql_desc', 'tool_sysroledatabase'), ''));

    $settings->add(new admin_setting_configcheckbox('tool_sysroledatabase/dbsybasequoting',
        get_string('dbsybasequoting', 'tool_sysroledatabase'),
        get_string('dbsybasequoting_desc', 'tool_sysroledatabase'), 0));

    $settings->add(new admin_setting_configcheckbox('tool_sysroledatabase/debugdb',
        get_string('debugdb', 'tool_sysroledatabase'),
        get_string('debugdb_desc', 'tool_sysroledatabase'), 0));

    $settings->add(new admin_setting_configtext('tool_sysroledatabase/minrecords',
        get_string('minrecords', 'tool_sysroledatabase'),
        get_string('minrecords_desc', 'tool_sysroledatabase'), 1));

    $settings->add(new admin_setting_heading('tool_sysroledatabase_localheader',
        get_string('settingsheaderlocal', 'tool_sysroledatabase'), ''));

    // Get all roles that can be assigned at the user context level and put their id's nicely into the configuration.
    $roleids = get_roles_for_contextlevels(CONTEXT_SYSTEM);
    list($insql, $inparams) = $DB->get_in_or_equal($roleids);
    $sql = "SELECT * FROM {role} WHERE id $insql";
    $roles = $DB->get_records_sql($sql, $inparams);
    $i = 1;
    foreach ($roles as $role) {
        $roleid[$i] = $role->id;
        $rolename[$i] = $role->shortname;
        $i++;
    }
    $rolenames = array_combine($roleid, $rolename);
    $settings->add(new admin_setting_configmultiselect('tool_sysroledatabase/syncroles',
        get_string('syncroles', 'tool_sysroledatabase'), '', array_keys($rolenames), $rolenames));

    $options = array('id' => 'id', 'idnumber' => 'idnumber', 'email' => 'email', 'username' => 'username');
    $settings->add(new admin_setting_configselect('tool_sysroledatabase/localuserfield',
        get_string('localuserfield', 'tool_sysroledatabase'), '', 'idnumber', $options));

    $settings->add(new admin_setting_heading('tool_sysroledatabase_remoteheader',
        get_string('settingsheaderremote', 'tool_sysroledatabase'), ''));

    $settings->add(new admin_setting_configtext('tool_sysroledatabase/remotetable',
        get_string('remotetable', 'tool_sysroledatabase'),
        get_string('remotetable_desc', 'tool_sysroledatabase'), ''));

    //e.g. 43563
    $settings->add(new admin_setting_configtext('tool_sysroledatabase/userfield',
        get_string('userfield', 'tool_sysroledatabase'),
        get_string('userfield_desc', 'tool_sysroledatabase'), ''));

    // Role shortname
    $settings->add(new admin_setting_configtext('tool_sysroledatabase/rolefield',
        get_string('rolefield', 'tool_sysroledatabase'),
        get_string('rolefield_desc', 'tool_sysroledatabase'), ''));

    $options = array(0  => get_string('removeroleassignment', 'tool_sysroledatabase'),
                     1  => get_string('keeproleassignment', 'tool_sysroledatabase'));
    $settings->add(new admin_setting_configselect('tool_sysroledatabase/removeaction',
        get_string('removedaction', 'tool_sysroledatabase'),
        get_string('removedaction_desc', 'tool_sysroledatabase'), 0, $options));

}
