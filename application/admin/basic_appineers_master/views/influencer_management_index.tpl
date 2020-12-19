<%if $this->input->is_ajax_request()%>
    <%$this->js->clean_js()%>
<%/if%>
<div class="module-list-container">
    <%include file="influencer_management_index_strip.tpl"%>
    <!-- Grid View HTML Block -->
    <div id="layout_grid_list2" class="perm-elem-hide">                        
        <style>
            .payroll-box-inn{height:290px !important;}
            /*.uigrid-status-btn-active,.uigrid-status-btn-inactive,.uigrid-status-btn-archived{display: none;}*/
        </style>
    </div>
    <div class="<%$module_name%>" data-list-name="<%$module_name%>">
        <div id="ajax_content_div" class="ajax-content-div top-frm-spacing box gradient">
            <!-- Page Loader -->
            <div id="ajax_qLoverlay"></div>
            <div id="ajax_qLbar"></div>
            <!-- Middle Content -->
            <div id="scrollable_content" class="scrollable-content top-list-spacing">
                <div class="grid-data-container pad-calc-container">
                    <div class="top-list-tab-layout" id="top_list_grid_layout">
                    </div>
                    <table class="grid-table-view " width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <!-- Module Listing Block -->
                            <td id="grid_data_col" class="<%$rl_theme_arr['grid_search_toolbar']%>">
                                <div id="pager2"></div>
                                <table id="list2"></table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" name="selAllRows" value="" id="selAllRows" />
    </div>
</div>
<!-- Module Listing Javascript -->
<%javascript%>
    $.jgrid.no_legacy_api = true; $.jgrid.useJSON = true;
    var el_grid_settings = {}, js_col_model_json = {}, js_col_name_json = {}; 
                    
    el_grid_settings['module_name'] = '<%$module_name%>';
    el_grid_settings['extra_hstr'] = '<%$extra_hstr%>';
    el_grid_settings['extra_qstr'] = '<%$extra_qstr%>';
    el_grid_settings['enc_location'] = '<%$enc_loc_module%>';
    el_grid_settings['par_module '] = '<%$this->general->getAdminEncodeURL($parMod)%>';
    el_grid_settings['par_data'] = '<%$this->general->getAdminEncodeURL($parID)%>';
    el_grid_settings['par_field'] = '<%$parField%>';
    el_grid_settings['par_type'] = 'parent';

    el_grid_settings['index_page_url'] = '<%$mod_enc_url["index"]%>';
    el_grid_settings['add_page_url'] = '<%$mod_enc_url["add"]%>'; 
    el_grid_settings['edit_page_url'] =  admin_url+'<%$mod_enc_url["inline_edit_action"]%>?<%$extra_qstr%>';
    el_grid_settings['listing_url'] = admin_url+'<%$mod_enc_url["listing"]%>?<%$extra_qstr%>';
   
        
    el_grid_settings['search_refresh_url'] = admin_url+'<%$mod_enc_url["get_left_search_content"]%>?<%$extra_qstr%>';
    el_grid_settings['search_autocomp_url'] = admin_url+'<%$mod_enc_url["get_search_auto_complete"]%>?<%$extra_qstr%>';
    el_grid_settings['ajax_data_url'] = admin_url+'<%$mod_enc_url["get_chosen_auto_complete"]%>?<%$extra_qstr%>';
    el_grid_settings['auto_complete_url'] = admin_url+'<%$mod_enc_url["get_token_auto_complete"]%>?<%$extra_qstr%>';
    el_grid_settings['subgrid_listing_url'] =  admin_url+'<%$mod_enc_url["get_subgrid_block"]%>?<%$extra_qstr%>';
    el_grid_settings['jparent_switchto_url'] = admin_url+'<%$parent_switch_cit["url"]%>?<%$extra_qstr%>';
    
    el_grid_settings['admin_rec_arr'] = $.parseJSON('<%$hide_admin_rec|@json_encode%>');;
    el_grid_settings['status_arr'] = $.parseJSON('<%$status_array|@json_encode%>');
    el_grid_settings['status_lang_arr'] = $.parseJSON('<%$status_label|@json_encode%>');
                
    el_grid_settings['hide_add_btn'] = '1';
    el_grid_settings['hide_del_btn'] = '0';
    el_grid_settings['hide_status_btn'] = '1';
    el_grid_settings['hide_export_btn'] = '';
    el_grid_settings['hide_columns_btn'] = 'Yes';
    
    el_grid_settings['show_saved_search'] = 'No';
    el_grid_settings['hide_advance_search'] = 'Yes';
    el_grid_settings['hide_search_tool'] = 'No';
    el_grid_settings['hide_multi_select'] = 'No';
    el_grid_settings['hide_paging_btn'] = 'No';
    el_grid_settings['hide_refresh_btn'] = 'Yes';
    
    el_grid_settings['popup_add_form'] = 'No';
    el_grid_settings['popup_edit_form'] = 'No';
    el_grid_settings['popup_add_size'] = ['75%', '75%'];
    el_grid_settings['popup_edit_size'] = ['75%', '75%'];
    
    el_grid_settings['permit_add_btn'] = '<%$add_access%>';
    el_grid_settings['permit_del_btn'] = '<%$del_access%>';
    el_grid_settings['permit_edit_btn'] = '<%$edit_access%>';
    el_grid_settings['permit_view_btn'] = '<%$view_access%>';
    el_grid_settings['permit_expo_btn'] = '<%$expo_access%>';
    el_grid_settings['permit_print_btn'] = '<%$print_access%>';
        
    el_grid_settings['group_search'] = '';
    el_grid_settings['default_sort'] = 'i_added_at';
    el_grid_settings['sort_order'] = 'desc';
    el_grid_settings['footer_row'] = 'No';
    el_grid_settings['grouping'] = 'No';
    el_grid_settings['group_attr'] = {};
    
    el_grid_settings['inline_add'] = 'No';
    el_grid_settings['rec_position'] = 'Top';
    el_grid_settings['auto_width'] = 'Yes';
    el_grid_settings['auto_refresh'] = 'No';
    el_grid_settings['lazy_loading'] = 'No';
    el_grid_settings['print_rec'] = 'No';
    el_grid_settings['print_list'] = 'No';
    
    el_grid_settings['subgrid'] = 'No';
    el_grid_settings['colgrid'] = 'No';
    el_grid_settings['listview'] = 'list';
    el_grid_settings['rating_allow'] = 'No';
    el_grid_settings['global_filter'] = 'No';
    
    el_grid_settings['search_slug'] = '<%$search_slug%>';
    el_grid_settings['search_list'] = $.parseJSON('<%$search_preferences|@json_encode%>');
    el_grid_settings['filters_arr'] = $.parseJSON('<%$default_filters|@json_encode%>');
    el_grid_settings['top_filter'] = [];
    el_grid_settings['buttons_arr'] = [];
    el_grid_settings['callbacks'] = [];
    el_grid_settings['message_arr'] = {
        "delete_alert" : "<%$this->general->processMessageLabel('ACTION_PLEASE_SELECT_ANY_RECORD')%>",
        "delete_popup" : "<%$this->general->processMessageLabel('ACTION_ARE_YOU_SURE_WANT_TO_DELETE_THIS_RECORD_C63')%>",
        "status_alert" : "<%$this->general->processMessageLabel('ACTION_PLEASE_SELECT_ANY_RECORD_TO__C35STATUS_C35')%>",
        "status_popup" : "<%$this->general->processMessageLabel('ACTION_ARE_YOU_SURE_WANT_TO__C35STATUS_C35_THIS_RECORDS_C63')%>",
    };
    
    js_col_name_json = [{
        "name": "i_influencer_image",
        "label": "<%$list_config['i_influencer_image']['label_lang']%>"
    },
    {
        "name": "i_influencer_name",
        "label": "<%$list_config['i_influencer_name']['label_lang']%>"
    },
    {
        "name": "i_influencer_code",
        "label": "<%$list_config['i_influencer_code']['label_lang']%>"
    },
    {
        "name": "i_influencer_status",
        "label": "<%$list_config['i_influencer_status']['label_lang']%>"
    },
    {
        "name": "i_added_at",
        "label": "<%$list_config['i_added_at']['label_lang']%>"
    },
    {
        "name": "sys_custom_field_1",
        "label": "<%$list_config['sys_custom_field_1']['label_lang']%>"
    }

    ];
    
    js_col_model_json = [{
        "name": "i_influencer_image",
        "index": "i_influencer_image",
        "label": "<%$list_config['i_influencer_image']['label_lang']%>",
        "labelClass": "header-align-center",
        "resizable": true,
        "width": "<%$list_config['i_influencer_image']['width']%>",
        "search": false,
        "export": <%if $list_config['i_influencer_image']['export'] eq 'No' %>false<%else%>true<%/if%>,
        "sortable": <%if $list_config['i_influencer_image']['sortable'] eq 'No' %>false<%else%>true<%/if%>,
        "hidden": <%if $list_config['i_influencer_image']['hidden'] eq 'Yes' %>true<%else%>false<%/if%>,
        "hideme": <%if $list_config['i_influencer_image']['hideme'] eq 'Yes' %>true<%else%>false<%/if%>,
        "addable": <%if $list_config['i_influencer_image']['addable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "editable": <%if $list_config['i_influencer_image']['editable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "align": "center",
        "edittype": "file",
        "editrules": {
            "infoArr": []
        },
        "searchoptions": {
            "attr": {
                "aria-grid-id": el_tpl_settings.main_grid_id,
                "aria-module-name": "influencer_management",
                "aria-unique-name": "i_influencer_image",
                "autocomplete": "off"
            },
            "sopt": strSearchOpts,
            "searchhidden": <%if $list_config['i_influencer_image']['search'] eq 'Yes' %>true<%else%>false<%/if%>
        },
        "editoptions": {
            "aria-grid-id": el_tpl_settings.main_grid_id,
            "aria-module-name": "influencer_management",
            "aria-unique-name": "i_influencer_image",
            "class": "inline-edit-row"
        },
        "ctrl_type": "file",
        "default_value": "<%$list_config['i_influencer_image']['default']%>",
        "filterSopt": "cn"
    },
    {
        "name": "i_influencer_name",
        "index": "i_influencer_name",
        "label": "<%$list_config['i_influencer_name']['label_lang']%>",
        "labelClass": "header-align-left",
        "resizable": true,
        "width": "<%$list_config['i_influencer_name']['width']%>",
        "search": <%if $list_config['i_influencer_name']['search'] eq 'No' %>false<%else%>true<%/if%>,
        "export": <%if $list_config['i_influencer_name']['export'] eq 'No' %>false<%else%>true<%/if%>,
        "sortable": <%if $list_config['i_influencer_name']['sortable'] eq 'No' %>false<%else%>true<%/if%>,
        "hidden": <%if $list_config['i_influencer_name']['hidden'] eq 'Yes' %>true<%else%>false<%/if%>,
        "hideme": <%if $list_config['i_influencer_name']['hideme'] eq 'Yes' %>true<%else%>false<%/if%>,
        "addable": <%if $list_config['i_influencer_name']['addable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "editable": <%if $list_config['i_influencer_name']['editable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "align": "left",
        "edittype": "text",
        "editrules": {
            "required": true,
            "infoArr": {
                "required": {
                    "message": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE__C35FIELD_C35_FIELD_C46 ,"#FIELD#",js_lang_label.INFLUENCER_MANAGEMENT_NAME)
                }
            }
        },
        "searchoptions": {
            "attr": {
                "aria-grid-id": el_tpl_settings.main_grid_id,
                "aria-module-name": "influencer_management",
                "aria-unique-name": "i_influencer_name",
                "autocomplete": "off"
            },
            "sopt": strSearchOpts,
            "searchhidden": <%if $list_config['i_influencer_name']['search'] eq 'Yes' %>true<%else%>false<%/if%>
        },
        "editoptions": {
            "aria-grid-id": el_tpl_settings.main_grid_id,
            "aria-module-name": "influencer_management",
            "aria-unique-name": "i_influencer_name",
            "placeholder": "",
            "class": "inline-edit-row "
        },
        "ctrl_type": "textbox",
        "default_value": "<%$list_config['i_influencer_name']['default']%>",
        "filterSopt": "bw",
        "formatter": formatAdminModuleEditLink,
        "unformat": unformatAdminModuleEditLink
    },  
    {
        "name": "i_influencer_code",
        "index": "i_influencer_code",
        "label": "<%$list_config['i_influencer_code']['label_lang']%>",
        "labelClass": "header-align-left",
        "resizable": true,
        "width": "<%$list_config['i_influencer_code']['width']%>",
        "search": <%if $list_config['i_influencer_code']['search'] eq 'No' %>false<%else%>true<%/if%>,
        "export": <%if $list_config['i_influencer_code']['export'] eq 'No' %>false<%else%>true<%/if%>,
        "sortable": <%if $list_config['i_influencer_code']['sortable'] eq 'No' %>false<%else%>true<%/if%>,
        "hidden": <%if $list_config['i_influencer_code']['hidden'] eq 'Yes' %>true<%else%>false<%/if%>,
        "hideme": <%if $list_config['i_influencer_code']['hideme'] eq 'Yes' %>true<%else%>false<%/if%>,
        "addable": <%if $list_config['i_influencer_code']['addable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "editable": <%if $list_config['i_influencer_code']['editable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "align": "left",
        "edittype": "text",
        "editrules": {
            "required": true,
            "infoArr": {
                "required": {
                    "message": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE__C35FIELD_C35_FIELD_C46 ,"#FIELD#",js_lang_label.INFLUENCER_MANAGEMENT_CODE)
                }
            }
        },
        "searchoptions": {
            "attr": {
                "aria-grid-id": el_tpl_settings.main_grid_id,
                "aria-module-name": "influencer_management",
                "aria-unique-name": "i_influencer_code",
                "autocomplete": "off"
            },
            "sopt": strSearchOpts,
            "searchhidden": <%if $list_config['i_influencer_code']['search'] eq 'Yes' %>true<%else%>false<%/if%>
        },
        "editoptions": {
            "aria-grid-id": el_tpl_settings.main_grid_id,
            "aria-module-name": "influencer_management",
            "aria-unique-name": "i_influencer_code",
            "placeholder": "",
            "class": "inline-edit-row "
        },
        "ctrl_type": "textbox",
        "default_value": "<%$list_config['i_influencer_code']['default']%>",
        "filterSopt": "bw",
        "formatter": formatAdminModuleEditLink,
        "unformat": unformatAdminModuleEditLink
    },    
    {
        "name": "i_influencer_status",
        "index": "i_influencer_status",
        "label": "<%$list_config['i_influencer_status']['label_lang']%>",
        "labelClass": "header-align-center",
        "resizable": true,
        "width": "<%$list_config['i_influencer_status']['width']%>",
        "search": <%if $list_config['i_influencer_status']['search'] eq 'No' %>false<%else%>true<%/if%>,
        "export": <%if $list_config['i_influencer_status']['export'] eq 'No' %>false<%else%>true<%/if%>,
        "sortable": <%if $list_config['i_influencer_status']['sortable'] eq 'No' %>false<%else%>true<%/if%>,
        "hidden": <%if $list_config['i_influencer_status']['hidden'] eq 'Yes' %>true<%else%>false<%/if%>,
        "hideme": <%if $list_config['i_influencer_status']['hideme'] eq 'Yes' %>true<%else%>false<%/if%>,
        "addable": <%if $list_config['i_influencer_status']['addable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "editable": <%if $list_config['i_influencer_status']['editable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "align": "center",
        "edittype": "select",
        "editrules": {
            "required": true,
            "infoArr": {
                "required": {
                    "message": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE__C35FIELD_C35_FIELD_C46 ,"#FIELD#",js_lang_label.INFLUENCER_MANAGEMENT_STATUS)
                }
            }
        },
        "searchoptions": {
            "attr": {
                "aria-grid-id": el_tpl_settings.main_grid_id,
                "aria-module-name": "influencer_management",
                "aria-unique-name": "i_influencer_status",
                "autocomplete": "off",
                "data-placeholder": " ",
                "class": "search-chosen-select",
                "multiple": "multiple"
            },
            "sopt": intSearchOpts,
            "searchhidden": <%if $list_config['i_influencer_status']['search'] eq 'Yes' %>true<%else%>false<%/if%>,
            "dataUrl": <%if $count_arr["i_influencer_status"]["json"] eq "Yes" %>false<%else%>'<%$admin_url%><%$mod_enc_url["get_list_options"]%>?alias_name=i_influencer_status&mode=<%$mod_enc_mode["Search"]%>&rformat=html<%$extra_qstr%>'<%/if%>,
            "value": <%if $count_arr["i_influencer_status"]["json"] eq "Yes" %>$.parseJSON('<%$count_arr["i_influencer_status"]["data"]|@addslashes%>')<%else%>null<%/if%>,
            "dataInit": <%if $count_arr['i_influencer_status']['ajax'] eq 'Yes' %>initSearchGridAjaxChosenEvent<%else%>initGridChosenEvent<%/if%>,
            "ajaxCall": '<%if $count_arr["i_influencer_status"]["ajax"] eq "Yes" %>ajax-call<%/if%>',
            "multiple": true
        },
        "editoptions": {
            "aria-grid-id": el_tpl_settings.main_grid_id,
            "aria-module-name": "influencer_management",
            "aria-unique-name": "i_influencer_status",
            "dataUrl": '<%$admin_url%><%$mod_enc_url["get_list_options"]%>?alias_name=i_influencer_status&mode=<%$mod_enc_mode["Update"]%>&rformat=html<%$extra_qstr%>',
            "dataInit": <%if $count_arr['i_influencer_status']['ajax'] eq 'Yes' %>initEditGridAjaxChosenEvent<%else%>initGridChosenEvent<%/if%>,
            "ajaxCall": '<%if $count_arr["i_influencer_status"] eq "Yes" %>ajax-call<%/if%>',
            "data-placeholder": "<%$this->general->parseLabelMessage('GENERIC_PLEASE_SELECT__C35FIELD_C35' ,'#FIELD#', 'INFLUENCER_MANAGEMENT_STATUS')%>",
            "class": "inline-edit-row chosen-select"
        },
        "ctrl_type": "dropdown",
        "default_value": "<%$list_config['i_influencer_status']['default']%>",
        "filterSopt": "in",
        "stype": "select"
    },
    {
        "name": "i_added_at",
        "index": "i_added_at",
        "label": "<%$list_config['i_added_at']['label_lang']%>",
        "labelClass": "header-align-left",
        "resizable": true,
        "width": "<%$list_config['i_added_at']['width']%>",
        "search": <%if $list_config['i_added_at']['search'] eq 'No' %>false<%else%>true<%/if%>,
        "export": <%if $list_config['i_added_at']['export'] eq 'No' %>false<%else%>true<%/if%>,
        "sortable": <%if $list_config['i_added_at']['sortable'] eq 'No' %>false<%else%>true<%/if%>,
        "hidden": <%if $list_config['i_added_at']['hidden'] eq 'Yes' %>true<%else%>false<%/if%>,
        "hideme": <%if $list_config['i_added_at']['hideme'] eq 'Yes' %>true<%else%>false<%/if%>,
        "addable": <%if $list_config['i_added_at']['addable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "editable": <%if $list_config['i_added_at']['editable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "align": "left",
        "edittype": "text",
        "editrules": {
            "infoArr": []
        },
        "searchoptions": {
            "attr": {
                "aria-grid-id": el_tpl_settings.main_grid_id,
                "aria-module-name": "influencer_management",
                "aria-unique-name": "i_added_at",
                "autocomplete": "off",
                "class": "search-inline-date",
                "aria-date-format": "YYYY-MM-DD"
            },
            "sopt": dateSearchOpts,
            "searchhidden": <%if $list_config['i_added_at']['search'] eq 'Yes' %>true<%else%>false<%/if%>,
            "dataInit": initSearchGridDateRangePicker
        },
        "editoptions": {
            "aria-grid-id": el_tpl_settings.main_grid_id,
            "aria-module-name": "influencer_management",
            "aria-unique-name": "i_added_at",
            "aria-date-format": "yy-mm-dd",
            "aria-min": "",
            "aria-max": "",
            "placeholder": "",
            "class": "inline-edit-row inline-date-edit date-picker-icon dateOnly"
        },
        "ctrl_type": "date",
        "default_value": "<%$list_config['i_added_at']['default']%>",
        "filterSopt": "bt"
    },
    {
        "name": "sys_custom_field_1",
        "index": "sys_custom_field_1",
        "label": "<%$list_config['sys_custom_field_1']['label_lang']%>",
        "labelClass": "header-align-center",
        "resizable": true,
        "width": "<%$list_config['sys_custom_field_1']['width']%>",
        "search": <%if $list_config['sys_custom_field_1']['search'] eq 'No' %>false<%else%>true<%/if%>,
        "export": <%if $list_config['sys_custom_field_1']['export'] eq 'No' %>false<%else%>true<%/if%>,
        "sortable": <%if $list_config['sys_custom_field_1']['sortable'] eq 'No' %>false<%else%>true<%/if%>,
        "hidden": <%if $list_config['sys_custom_field_1']['hidden'] eq 'Yes' %>true<%else%>false<%/if%>,
        "hideme": <%if $list_config['sys_custom_field_1']['hideme'] eq 'Yes' %>true<%else%>false<%/if%>,
        "addable": <%if $list_config['sys_custom_field_1']['addable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "editable": <%if $list_config['sys_custom_field_1']['editable'] eq 'Yes' %>true<%else%>false<%/if%>,
        "align": "center",
        "edittype": "select",
        "editrules": {
            "infoArr": []
        },
        "searchoptions": {
            "attr": {
                "aria-grid-id": el_tpl_settings.main_grid_id,
                "aria-module-name": "influencer_management",
                "aria-unique-name": null,
                "autocomplete": "off"
            },
            "sopt": strSearchOpts,
            "searchhidden": <%if $list_config['sys_custom_field_1']['search'] eq 'Yes' %>true<%else%>false<%/if%>
        },
        "editoptions": {
            "aria-grid-id": el_tpl_settings.main_grid_id,
            "aria-module-name": "influencer_management",
            "aria-unique-name": null,
            "placeholder": null,
            "class": "inline-edit-row "
        },
        "ctrl_type": "textbox",
        "default_value": "<%$list_config['sys_custom_field_1']['default']%>",
        "filterSopt": "bw"
    }];
         
    initMainGridListing();
    createTooltipHeading();
    callSwitchToParent();
<%/javascript%>
<%$this->js->add_js("admin/custom/feedbackManagement.js")%>
<%if $this->input->is_ajax_request()%>
    <%$this->js->js_src()%>
<%/if%> 
<%if $this->input->is_ajax_request()%>
    <%$this->css->css_src()%>
<%/if%> 