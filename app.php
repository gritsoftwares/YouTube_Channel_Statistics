<?php

require_once 'config.php';
$api_key = $yt_api;

$sql = "SELECT * FROM yt_channels";
$result = mysqli_query($link, $sql);
if (mysqli_num_rows($result) > 0) {

    while ($channel_data = mysqli_fetch_array($result)) {
        $channel_list[$channel_data['id']] = $channel_data['channel_id'];
    }

    foreach ($channel_list as $id => $channel_id) {
        $url = 'https://www.googleapis.com/youtube/v3/channels?part=statistics&id=' . $channel_id . '&key=' . $api_key;

        $sql = "SELECT * FROM yt_channel_data WHERE `yt_channel_id` = $id ORDER BY `date` DESC LIMIT 1";
        $result = mysqli_query($link, $sql);
        $result = mysqli_fetch_array($result);

        $data = json_decode(file_get_contents($url));
        $viewCount = $data->items[0]->statistics->viewCount;
        $subscriberCount = $data->items[0]->statistics->subscriberCount;
        $videoCount = $data->items[0]->statistics->videoCount;

        $todaysView = $viewCount - $result['viewCount'];
        $todaysSubscriber = $subscriberCount - $result['subscriberCount'];
        $todaysVideo = $videoCount - $result['videoCount'];

        $sql = "INSERT INTO yt_channel_data ( ";
        $sql .= "`yt_channel_id`, `viewCount`, `subscriberCount`, `videoCount`, `todaysView`, `todaysSubscriber`, `todaysVideo` ";
        $sql .= ") VALUES ( ";
        $sql .= "$id, $viewCount, $subscriberCount, $videoCount, $todaysView, $todaysSubscriber, $todaysVideo ";
        $sql .= ")";

        $result = mysqli_query($link, $sql);
        if ($result) {
            echo $id . ': Successfull' . '<br/>';
        } else {
            echo $id . ': Failed' . '<br/>';
        }
    }
}  else {
    echo 'No Channels Found!! <a href="addChannel.php">Add some channel</a> and come back.';
}
