<?php require_once __DIR__ . '/includes/_header.php';?>
<?php
/**
 * this code is responsible for listing the content in admin dash
 */
?>

<?php
if ($_GET['type'] == 'key_value_pair' || $_GET['type'] == 'api_key_secret') {
    ob_start();
    header('Location: /admin/meta');
}
?>

<div class="p-3">
    <?php
if ($_GET['role']) {
    $role = $types['user']['roles'][$_GET[role]];
}

if (
    isset($types['user']['roles_restricted_within_matching_modules']) &&
    $types['user']['roles_restricted_within_matching_modules']
) {
    $user_restricted_to_input_modules = array_intersect(array_keys($currentUser), array_keys($types));
}

echo $admin->get_admin_menu('list', $type, $role['slug']);
?>


    <h2 class="mb-4">
        <?php if ($type == 'user'): ?>
            <?=$role['title']?>&nbsp;
            <small><span class="fas fa-angle-double-right"></span></small>&nbsp;
        <?php endif;?>

        List of <?=$types[$type]['plural']?>
    </h2>

    <table class="my-4 table table-borderless table-hover datatable">
        <thead class="thead-black">
            <tr>
                <th scope="col">#</th>
                <?php
$i = 0;
$displayed_field_slugs = array();

foreach ($types[$type]['modules'] as $module):
    if (!in_array($module['input_slug'], $displayed_field_slugs)):
        if (isset($module['list_field']) && $module['list_field']):
        ?><th scope="col" class="pl-2" data-orderable="<?=isset($module['list_sortable']) ? $module['list_sortable'] : 'false'?>" data-searchable="<?=isset($module['list_searchable']) ? $module['list_searchable'] : 'false'?>" style="<?=(isset($module['input_primary']) && $module['input_primary']) ? 'max-width:50%' : ''?>"><?=$module['input_slug']?></th><?php
    endif;
    $displayed_field_slugs[] = $module['input_slug'];
endif;
$i++;
endforeach;
?>
                <th scope="col" data-orderable="false" data-searchable="false"></th>
            </tr>
        </thead>

        <tbody>
            <?php
if ($type == 'user') {
    $ids = $dash->get_all_ids(array('type' => $type, 'role_slug' => $_GET['role']));
} else {
    $ids = $dash->get_all_ids($type);
}

foreach ($ids as $arr) {
    //$post = $dash->get_content($arr['id']);

    if (
        isset($types['user']['roles_restricted_within_matching_modules']) &&
        $types['user']['roles_restricted_within_matching_modules'] &&
        !$admin->is_access_allowed($arr['id'], $user_restricted_to_input_modules)
    ) {
        continue;
    }

    $post = array();
    $post['id'] = $arr['id'];
    $post['type'] = $type;
    $post['slug'] = $dash->get_content_meta($post['id'], 'slug');

    $tr_echo = '<tr><th scope="row">' . $post['id'] . '</th>';

    $donotlist = 0;
    foreach ($types[$type]['modules'] as $module) {
        if (isset($module['list_field']) && $module['list_field']) {
            $module_input_slug_lang = $module['input_slug'] . (is_array($module['input_lang']) ? '_' . $module['input_lang'][0]['slug'] : '');
            $cont = $dash->get_content_meta($post['id'], $module_input_slug_lang);
            $tr_echo .= '<td>' . $cont . '</td>';
            if ($module['list_non_empty_only'] && !trim($cont)) {
                $donotlist = 1;
            }
        }
    }

    // edit and view buttons
    $tr_echo .= '<td><span class="d-flex">' . (($currentUser['role'] == 'admin' || $currentUser['user_id'] == $dash->get_content_meta($post['id'], 'user_id')) ? '<a class="mr-1" title="Edit" href="/admin/edit?type=' . $post['type'] . '&id=' . $post['id'] . ($type == 'user' ? '&role=' . $_GET['role'] : '') . '"><i class="fas fa-edit"></i></a>&nbsp;' : '') . '<a title="View" target="new" href="/' . $post['type'] . '/' . $post['slug'] . '"><i class="fas fa-external-link-alt"></i></a></span></td></tr>';

    if (!$donotlist) {
        echo $tr_echo;
    }
}
?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/_footer.php';?>
