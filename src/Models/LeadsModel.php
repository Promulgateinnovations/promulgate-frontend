<?php

namespace Promulgate\Models;

class LeadsModel extends BaseModel
{
    public function __construct()
	{
		parent::__construct();

	}

    public function saveLeadsDetails(array $all_input)
    { 
       $this->makeRequest('POST', '/api/v1/uploadLeadsData', [
            'json' => [
                "all_input" => $all_input,
                "excel-file" => $all_input['file'],
                "source" => $all_input['source'],
                "description" => $all_input['desc'],
                "broadcast" => $all_input['broadcast']
            ],
          ]
        );
    }

    public function getLeadsDetails()
    {
		return $this->makeRequest('POST','/api/v1/getLeadDetails',[
            'json' => [],
        ]
      );
	}

    public function getBroadcastedLeads()
    {
		return $this->makeRequest('POST','/api/v1/getBroadcastedLeads',[
            'json' => [],
        ]
      );
	}

    public function getLeadContacts($lead_id){
        return $this->makeRequest('POST','/api/v1/getLeadContacts',[
            'json' => [
                "lead_id" => $lead_id,
            ],
        ]);
    }

    public function getTemplates($content_to_post)
    {
    	if(!$content_to_post) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST','/api/v1/getTemplates', [
				'json' => $content_to_post,
			]
		);
	}

    public function addTemplate($content_to_post)
	{

		if(!$content_to_post) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/addNewWaTemplate', [
				'json' => $content_to_post,
			]
		);

	}

    public function saveContent($content_to_post)
	{

		if(!$content_to_post) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/broadcastWhatsappMessages', [
				'json' => $content_to_post,
			]
		);

	}

    public function checkFbToken($content_to_post)
	{

		if(!$content_to_post) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/checkToken', [
				'json' => $content_to_post,
			]
		);

	}

	public function deleteLead($content_to_post)
	{

		if(!$content_to_post) {
			return [
				'body' => [],
			];
		}

		return $this->makeRequest('POST', '/api/v1/deleteLead', [
				'json' => $content_to_post,
			]
		);

	}
}