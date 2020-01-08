<?php

namespace AsaEs\Sdk\Baseservice\Messagebox;


use App\AppConst\AppInfo;
use AsaEs\Utility\Time;

class MessageboxBean implements \Serializable {

    protected $title;    // 主标题
    protected $title_icon;    // 主标题icon
    protected $subheading;    // 副标题
    protected $subheading_icon;    // 副标题icon
    protected $intro;    // 简介
    protected $intro_icon;    // 简介icon
    protected $content;    // 内容
    protected $content_icon;    // 内容icon
    protected $type;    // 消息类型（预留字段）
    protected $type_name;    // 消息类型名称（预留字段）
    protected $other_data;    // 其它数据
    protected $from_user_id;    // 发送者用户编号
    protected $from_user_name;    // 发送者用户名称
    protected $to_user_id;    // 接收用户编号
    protected $to_user_name;    // 接受用户名称
    private $system_id;
    private $send_time;    // 发送时间


    function __construct()
    {}

    public function serialize()
    {}

    public function unserialize($serialized)
    {}

    private function initialize(){

        $this->send_time = Time::getNowDataTime();  // 默认为当前时间
        $this->system_id = AppInfo::SYSTEM_ID;
    }

    public function toArray(){

        // 帮你初始化数据
        $this->initialize();

        $data = [];
        foreach ($this as $key => $item) {
            $data[$key] = $item;
        }

        $this->clean();
        return $data;
    }

    private function clean(){

        foreach ($this as $key => $item) {
            $this->$key = null;
        }
    }

    /**
     * @param mixed $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param mixed $title_icon
     */
    public function setTitleIcon(string $title_icon): void
    {
        $this->title_icon = $title_icon;
    }

    /**
     * @param mixed $subheading
     */
    public function setSubheading(string $subheading): void
    {
        $this->subheading = $subheading;
    }

    /**
     * @param mixed $subheading_icon
     */
    public function setSubheadingIcon(string $subheading_icon): void
    {
        $this->subheading_icon = $subheading_icon;
    }

    /**
     * @param mixed $intro
     */
    public function setIntro(string $intro): void
    {
        $this->intro = $intro;
    }

    /**
     * @param mixed $intro_icon
     */
    public function setIntroIcon(string $intro_icon): void
    {
        $this->intro_icon = $intro_icon;
    }

    /**
     * @param mixed $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @param mixed $content_icon
     */
    public function setContentIcon(string $content_icon): void
    {
        $this->content_icon = $content_icon;
    }

    /**
     * @param mixed $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param mixed $type_name
     */
    public function setTypeName(string $type_name): void
    {
        $this->type_name = $type_name;
    }


    /**
     * @param mixed $other_data
     */
    public function setOtherData(string $other_data): void
    {
        $this->other_data = $other_data;
    }

    /**
     * @param mixed $from_user_id
     */
    public function setFromUserId(string $from_user_id): void
    {
        $this->from_user_id = $from_user_id;
    }

    /**
     * @param mixed $from_user_name
     */
    public function setFromUserName(string $from_user_name): void
    {
        $this->from_user_name = $from_user_name;
    }

    /**
     * @param mixed $to_user_id
     */
    public function setToUserId(string $to_user_id): void
    {
        $this->to_user_id = $to_user_id;
    }
    /**
     * @param mixed $to_user_name
     */
    public function setToUserName(string $to_user_name): void
    {
        $this->to_user_name = $to_user_name;
    }
}
