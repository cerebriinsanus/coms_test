<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SearchRequestDTO
{
    public const GROUP_LIST = 'list';
    public const GROUP_COUNTS = 'counts';
    const DIRECTIONS = ['ASC', 'DESC'];
    const ORDER_FIELDS = ['title', 'publishedAt'];

    /**
     * @Groups({SearchRequestDTO::GROUP_LIST})
     * @Assert\Type(type="integer", groups={SearchRequestDTO::GROUP_LIST})
     * @var integer
     */
    public $offset;

    /**
     * @Groups({SearchRequestDTO::GROUP_LIST})
     * @Assert\Type(type="integer", groups={SearchRequestDTO::GROUP_LIST})
     * @var integer
     */
    public $limit;

    /**
     * @Groups({SearchRequestDTO::GROUP_LIST})
     * @Assert\Type(type="string", groups={SearchRequestDTO::GROUP_LIST})
     * @Assert\Choice(
     *     choices=SearchRequestDTO::ORDER_FIELDS,
     *     message="Choose valid field for sorting",
     *     groups={SearchRequestDTO::GROUP_LIST}
     *     )
     * @var string
     */
    public $order_by;

    /**
     * @Groups({SearchRequestDTO::GROUP_LIST})
     * @Assert\Type(type="string", groups={SearchRequestDTO::GROUP_LIST})
     * @Assert\Choice(
     *     choices=SearchRequestDTO::DIRECTIONS,
     *     message="Choose either ASC or DESC.",
     *     groups={SearchRequestDTO::GROUP_LIST}
     *     )
     * @var string
     */
    public $order_dir;

    /**
     * @Groups({SearchRequestDTO::GROUP_LIST, SearchRequestDTO::GROUP_COUNTS})
     * @Assert\Type(type="\DateTimeInterface", groups={SearchRequestDTO::GROUP_LIST, SearchRequestDTO::GROUP_COUNTS})
     * @var \DateTime
     */
    public $date_start;

    /**
     * @Groups({SearchRequestDTO::GROUP_LIST, SearchRequestDTO::GROUP_COUNTS})
     * @Assert\Type(type="\DateTimeInterface", groups={SearchRequestDTO::GROUP_LIST, SearchRequestDTO::GROUP_COUNTS})
     * @var \DateTime
     */
    public $date_end;

    /**
     * @Groups({SearchRequestDTO::GROUP_LIST})
     * @Assert\Type(type="array", groups={SearchRequestDTO::GROUP_LIST})
     * @Assert\All(constraints={
     *     @Assert\NotBlank(),
     *     @Assert\Type(type="integer")
     * }, groups={SearchRequestDTO::GROUP_LIST})
     * @var array
     */
    public $ids;
}