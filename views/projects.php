<div class="wrap">

    <h1 class="wp-heading-inline">My projects</h1>
    <hr class="wp-header-end">

    <div class="tablenav top">

        Here you'll find a overview of all your curent campaigns in your FastPages-account.
        
        <div class="tablenav-pages one-page">
            <span class="displaying-num">
                <?php echo sizeof($projects); ?> item(s)
            </span>
        </div>

        <br class="clear">

    </div>

    <table class="wp-list-table widefat fixed striped posts">

        <thead>
            <tr>
                <th scope="col" id="title" class="manage-column column-title column-primary">
                    <span>Name</span>
                </th>
                <th scope="col" id="status" class="manage-column column-status">
                    <span>Status</span>
                </th>
                <th scope="col" id="domains" class="manage-column column-domains">
                    <span>Domains</span>
                </th>
                <th scope="col" id="actions" class="manage-column column-actions">
                    <span>Actions</span>
                </th>
            </tr>
        </thead>

        <tbody id="the-list">

            <?php foreach ($projects as $project) { ?>

                <tr class="entry">
                    <td class="column-title" data-colname="Title">
                        <strong>
                            <?php echo $project->name; ?>
                        </strong>
                        <?php echo $project->uploaded; ?> | <i>v<?php echo $project->version; ?></i>
                    </td>
                    <td class="column-status" data-colname="Status">
                        <?php if ($project->published === true) { ?>
                            <?php if (array_key_exists($project->uuid, $connected)) { ?>
                                <span class="dashicons dashicons-plus-alt"></span> Connected
                            <?php } else { ?>
                                <span class="dashicons dashicons-yes-alt"></span> Published
                            <?php } ?>
                        <?php } else { ?>
                            <span class="dashicons dashicons-marker"></span> Unpublished
                        <?php } ?>
                    </td>
                    <td class="column-domains" data-colname="Domains">
                        <?php if ($project->published === true) { ?>
                            <?php foreach ($project->domains as $domain) { ?>
                                <a href="https://<?php echo $domain->domain; ?>" target="_blank">
                                    https://<?php echo $domain->domain; ?>
                                </a>
                            <?php } ?>
                        <?php } else { ?>
                            —
                        <?php } ?>
                    </td>
                    <td class="column-actions" data-colname="Actions">
                        <?php if ($project->published === true) { ?>
                            <?php if (array_key_exists($project->uuid, $connected)) { ?>
                                <form method="post" action="admin.php?page=fastpages&action=modify">
                                    <input type="text" name="uuid" value="<?php echo $project->uuid; ?>" style="display: none;" />
                                    <input type="text" name="slug" value="<?php echo $connected[$project->uuid]; ?>" placeholder="" />
                                    <input type="submit" class="button button-primary" value="Modify" />
                                </form>
                                <hr />
                                <form method="get" action="admin.php?page=fastpages&action=disconnect" style="display: inline-block;">
                                <a class="button button-default" href="<?php echo get_site_url() . '/' . $connected[$project->uuid]; ?>" target="_blank">
                                    View
                                </a>
                                </form>
                                <form method="post" action="admin.php?page=fastpages&action=disconnect" style="display: inline-block;">
                                    <input type="text" name="uuid" value="<?php echo $project->uuid; ?>" style="display: none;" />
                                    <input type="submit" class="button button-default" value="Disconnect" />
                                </form>
                            <?php } else { ?>
                                <form method="post" action="admin.php?page=fastpages&action=connect">
                                    <input type="text" name="uuid" value="<?php echo $project->uuid; ?>" style="display: none;" />
                                    <input type="text" name="slug" value="" placeholder="" />
                                    <input type="submit" class="button button-primary" value="Connect" />
                                </form>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>

            <?php } ?>
            
        </tbody>

    </table>
    
    <hr />

    <a class="button button-primary" href="admin.php?page=fastpages&action=logout">
        Logout
    </a>
    
</div>