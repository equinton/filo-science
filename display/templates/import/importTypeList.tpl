<h2>{t}Liste des types d'import{/t}</h2>
{if $droits.param == 1}
<a href="index.php?module=importTypeChange&import_type_id=0">
    <img src="display/images/new.png" height="25">
    {t}Nouveau modèle{/t}
</a>
{/if}
<div class="row">
    <div class="col-md-6">
        <table id="importTypeList" class="table table-bordered table-hover datatable-nopaging ">
            <thead>
                <tr>
                    <th>{t}Id{/t}</th>
                    <th>{t}Nom{/t}</th>
                    <th>{t}Nom de la table dans la base de données{/t}</th>
                    <th>{t}Table de type entité-relation ?{/t}</th>
                    <th>{t}Liste des colonnes à renseigner{/t}</th>
                    {if $droits.param == 1}
                    <th class="center">
                        <img src="display/images/edit.gif" height="25" title="{t}Modifier{/t}">
                    </th>
                    {/if}
                </tr>
            </thead>
            <tbody>
                {foreach $imports as $import}
                <tr>
                    <td class="center">{$import.import_type_id}</td>
                    <td>{$import.import_type_name}</td>
                    <td>{$import.tablename}</td>
                    <td class="center">{if $import.is_table_er == 1}oui{/if}</td>
                    <td>{$import.column_list}</td>
                    {if $droits.param == 1}
                    <td class="center">
                        <a
                            href="index.php?module=importTypeChange&import_type_id={$import.import_type_id}">
                            <img src="display/images/edit.gif" height="25" title="{t}Modifier{/t}">
                        </a>
                    </td>
                    {/if}
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>