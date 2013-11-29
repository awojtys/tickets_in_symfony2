<?php
namespace Awojtys\TicketServBundle\DependencyInjection;

class AjaxHelper
{
    
    public function getArrayFromTicketData($data)
    {
        
        $all_data = array();
        foreach($data as $key => $value)
        {
            $all_data[$key] = array(
                'ID' => $value->getID(),
                'Author' => $value->getAuthor()->getNickname(),
                'Title' => $value->getTitle(),
                'Status' => $value->getStatus()->getName(),
                'Priority' => $value->getPriority()->getName(),
                'Assignee' => $value->getAssignee() != null ? $value->getAssignee()->getNickname() : '',
                'Date' => $value->getDate()->format('Y-m-d')
                    );
        }
        return $all_data;
    }
}    

?>
