import React, { useState, useEffect } from 'react';
import { makeStyles } from '@material-ui/core/styles';
import Card from '@material-ui/core/Card';
import CardContent from '@material-ui/core/CardContent';
import CardMedia from '@material-ui/core/CardMedia';
import Typography from '@material-ui/core/Typography';
import Grid from '@material-ui/core/Grid';
import { Scrollbars } from 'react-custom-scrollbars';
import { red } from '@material-ui/core/colors';
import Avatar from '@material-ui/core/Avatar';
import TextTruncate from 'react-text-truncate'; // recommend

import "./ConversationList.css"
import axios from 'axios';
import ChatUI from './../Chat/Chat';
import images from "./../Product/images";

const useStyles = makeStyles((theme) => ({
  root: {
    display: 'flex',
    padding: 5,
    alignItems: "center",
    '&:hover': { backgroundColor: '#88baef!important' },
  },

  details: {
    display: 'flex',
    flexDirection: 'column',
  },
  
  content: {
    flex: '1 0 auto',
  },
  
  media: {
    width: 100,
    height: 100,
    marginLeft: "auto",
    border: "2px solid pink"
  },

  avatar: {
    backgroundColor: red[500],
  },

}));

export default function ConversationList(props) {
  const classes = useStyles();
  const [conversations, setConversations] = useState([]);
  const [chat, setChat] = useState();

  useEffect(() => {
    setChat(props.chatId);
    axios.get(process.env.REACT_APP_BE_URL + 'chats', {
      headers: {
        'Authorization': `Bearer ${props.token}` 
      }
    }).then(function(response){
      setConversations([...response.data.data]);
    });
  }, []);

  return (
    <Grid container component="main">
      <Grid item xs={false} sm={4} md={4} className="inboxSector">
        <h3>Inbox</h3>
        <Scrollbars>
          {conversations.map(conversation => {
            let image = images["p" + conversation.listing_id];
            return (
              <Card key={conversation.chat_id} className={`${classes.root} ${chat === conversation.chat_id ? "active" : ""}`} onClick={(e)=>{setChat(conversation.chat_id);}}>
                  <Avatar aria-label="User" className={classes.avatar}>
                    {conversation.participants.length === 0 ? "" : conversation.participants.map(participant => {
                      return (
                        <span key={participant}> {participant.substring(0, 1)}</span>
                      );
                    }).reduce(
                      (prev, curr) => [prev, ', ', curr]
                    )}
                  </Avatar>
                <div className={classes.details}>
                  <CardContent className={classes.content}>
                    <Typography variant="subtitle1" color="textSecondary">
                      {conversation.participants.length === 0 ? "" : conversation.participants.map(participant => {
                        return (
                          <span key={participant}> {participant}</span>
                        );
                      }).reduce(
                        (prev, curr) => [prev, ', ', curr]
                      )}
                    </Typography>
                    <Typography component="h6" variant="h6">
                      <TextTruncate line = {1} truncateText="…" text = {conversation.listing_item} />
                    </Typography>
                    <Typography variant="subtitle1" color="textPrimary">
                      <TextTruncate line = {1} truncateText="…" text = {conversation.last_message} element = "p" />
                    </Typography>
                  </CardContent>
                </div>
                <CardMedia
                    className={classes.media}
                    image={image}
                    title={conversation.listing_item}
                  />
              </Card>
            );
          })}
        </Scrollbars>
      </Grid>
      <Grid item xs={12} sm={6} md={6}>
        {chat ? (
          <ChatUI key={chat} pusher={props.pusher} token={props.token} chatId={chat} userId={props.userId} ban_period={props.ban_period}/>
        ) : (
          <p>Please select a message to start</p>
        )}
      </Grid>
    </Grid>
  );
}
