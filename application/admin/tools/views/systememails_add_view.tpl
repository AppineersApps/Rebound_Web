<%if $this->input->is_ajax_request()%>
    <%$this->js->clean_js()%>
<%/if%>
<%if $this->input->is_ajax_request()%>
    <%$this->js->clean_js()%>
<%/if%>
<div class="module-view-container">
    <%include file="systememails_add_strip.tpl"%>
    <div class="<%$module_name%>" data-form-name="<%$module_name%>">
        <div id="ajax_content_div" class="ajax-content-div top-frm-spacing" >
            <input type="hidden" id="projmod" name="projmod" value="systememails" />
            <!-- Page Loader -->
            <div id="ajax_qLoverlay"></div>
            <div id="ajax_qLbar"></div>
            <!-- Module Tabs & Top Detail View -->
            <div class="top-frm-tab-layout" id="top_frm_tab_layout">
            </div>
            <!-- Middle Content -->
            <div id="scrollable_content" class="scrollable-content popup-content top-block-spacing ">
                <!-- Module View Block -->
                <div id="systememails" class="frm-module-block frm-view-block frm-stand-view">
                    <!-- Form Hidden Fields Unit -->
                    <input type="hidden" id="id" name="id" value="<%$enc_id%>" />
                    <input type="hidden" id="mode" name="mode" value="<%$mod_enc_mode[$mode]%>" />
                    <input type="hidden" id="ctrl_flow" name="ctrl_flow" value="<%$ctrl_flow%>" />
                    <input type="hidden" id="ctrl_prev_id" name="ctrl_prev_id" value="<%$next_prev_records['prev']['id']%>" />
                    <input type="hidden" id="ctrl_next_id" name="ctrl_next_id" value="<%$next_prev_records['next']['id']%>" />
                    <!-- Form Display Fields Unit -->
                    <div class="main-content-block " id="main_content_block">
                        <div style="width:98%;" class="frm-block-layout pad-calc-container">
                            <div class="box gradient <%$rl_theme_arr['frm_stand_content_row']%> <%$rl_theme_arr['frm_stand_border_view']%>">
                                <div class="title <%$rl_theme_arr['frm_stand_titles_bar']%>"><h4><%$this->lang->line('SYSTEMEMAILS_SYSTEM_EMAILS')%></h4></div>
                                <div class="content <%$rl_theme_arr['frm_stand_label_align']%>">
                                    <div class="form-row row-fluid " id="cc_sh_mse_email_code">
                                        <label class="form-label span3">
                                            <%$form_config['mse_email_code']['label_lang']%>
                                        </label> 
                                        <div class="form-right-div frm-elements-div ">
                                            <span class="frm-data-label"><strong><%$data['mse_email_code']%></strong></span>
                                        </div>
                                    </div>
                                    <div class="form-row row-fluid " id="cc_sh_mse_email_title">
                                        <label class="form-label span3">
                                            <%$form_config['mse_email_title']['label_lang']%>
                                        </label> 
                                        <div class="form-right-div frm-elements-div ">
                                            <span class="frm-data-label"><strong><%$data['mse_email_title']%></strong></span>
                                        </div>
                                    </div>
                                    <div class="form-row row-fluid " id="cc_sh_mse_from_name">
                                        <label class="form-label span3">
                                            <%$form_config['mse_from_name']['label_lang']%>
                                        </label> 
                                        <div class="form-right-div frm-elements-div ">
                                            <span class="frm-data-label"><strong><%$data['mse_from_name']%></strong></span>
                                        </div>
                                    </div>
                                    <div class="form-row row-fluid " id="cc_sh_mse_from_email">
                                        <label class="form-label span3">
                                            <%$form_config['mse_from_email']['label_lang']%>
                                        </label> 
                                        <div class="form-right-div frm-elements-div ">
                                            <span class="frm-data-label"><strong><%$data['mse_from_email']%></strong></span>
                                        </div>
                                    </div>
                                    <div class="form-row row-fluid " id="cc_sh_mse_bcc_email">
                                        <label class="form-label span3">
                                            <%$form_config['mse_bcc_email']['label_lang']%>
                                        </label> 
                                        <div class="form-right-div frm-elements-div ">
                                            <span class="frm-data-label"><strong><%$data['mse_bcc_email']%></strong></span>
                                        </div>
                                    </div>
                                    <div class="form-row row-fluid " id="cc_sh_mse_cc_email">
                                        <label class="form-label span3">
                                            <%$form_config['mse_cc_email']['label_lang']%>
                                        </label> 
                                        <div class="form-right-div frm-elements-div ">
                                            <span class="frm-data-label"><strong><%$data['mse_cc_email']%></strong></span>
                                        </div>
                                    </div>
                                    <div class="form-row row-fluid " id="cc_sh_mse_email_subject">
                                        <label class="form-label span3">
                                            <%$form_config['mse_email_subject']['label_lang']%>
                                        </label> 
                                        <div class="form-right-div frm-elements-div ">
                                            <span class="frm-data-label"><strong><%$data['mse_email_subject']%></strong></span>
                                        </div>
                                    </div>
                                    <div class="form-row row-fluid " id="cc_sh_mse_email_message">
                                        <label class="form-label span3">
                                            <%$form_config['mse_email_message']['label_lang']%>
                                        </label> 
                                        <div class="form-right-div frm-elements-div  frm-editor-layout">
                                            <span class="frm-data-label"><strong><%$data['mse_email_message']%></strong></span>
                                        </div>
                                    </div>
                                    <div class="form-row row-fluid " id="cc_sh_mse_status">
                                        <label class="form-label span3">
                                            <%$form_config['mse_status']['label_lang']%>
                                        </label> 
                                        <div class="form-right-div frm-elements-div ">
                                            <span class="frm-data-label"><strong><%$this->general->displayKeyValueData($data['mse_status'], $opt_arr['mse_status'])%></strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<%javascript%>        
            
    var el_form_settings = {}, elements_uni_arr = {}, child_rules_arr = {}, google_map_json = {}, pre_cond_code_arr = [];
    el_form_settings['module_name'] = '<%$module_name%>'; 
    el_form_settings['extra_hstr'] = '<%$extra_hstr%>';
    el_form_settings['extra_qstr'] = '<%$extra_qstr%>';
    el_form_settings['upload_form_file_url'] = admin_url+"<%$mod_enc_url['upload_form_file']%>?<%$extra_qstr%>";
    el_form_settings['get_chosen_auto_complete_url'] = admin_url+"<%$mod_enc_url['get_chosen_auto_complete']%>?<%$extra_qstr%>";
    el_form_settings['token_auto_complete_url'] = admin_url+"<%$mod_enc_url['get_token_auto_complete']%>?<%$extra_qstr%>";
    el_form_settings['tab_wise_block_url'] = admin_url+"<%$mod_enc_url['get_tab_wise_block']%>?<%$extra_qstr%>";
    el_form_settings['parent_source_options_url'] = "<%$mod_enc_url['parent_source_options']%>?<%$extra_qstr%>";
    el_form_settings['jself_switchto_url'] =  admin_url+'<%$switch_cit["url"]%>';
    el_form_settings['callbacks'] = [];

    callSwitchToSelf();
<%/javascript%>

<%if $this->input->is_ajax_request()%>
    <%$this->js->js_src()%>
<%/if%> 
<%if $this->input->is_ajax_request()%>
    <%$this->css->css_src()%>
<%/if%> 
