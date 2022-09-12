import React, { Component } from 'react';
import axios from 'axios';
import { makeStyles, withStyles } from '@material-ui/core/styles';
import Card from '@material-ui/core/Card';
import CardActionArea from '@material-ui/core/CardActionArea';
import CardActions from '@material-ui/core/CardActions';
import CardContent from '@material-ui/core/CardContent';
import CardMedia from '@material-ui/core/CardMedia';
import CardHeader from '@material-ui/core/CardHeader';
import Typography from '@material-ui/core/Typography';
import Grid from '@material-ui/core/Grid';
import Container from '@material-ui/core/Container';
import images from "./images";
import TextTruncate from 'react-text-truncate'; // recommend
import ReactTimeAgo from 'react-time-ago';
import TimeAgo from 'javascript-time-ago';
import en from 'javascript-time-ago/locale/en';
import { red } from '@material-ui/core/colors';
import Avatar from '@material-ui/core/Avatar';
import IconButton from '@material-ui/core/IconButton';
import FavoriteIcon from '@material-ui/icons/Favorite';
import ShareIcon from '@material-ui/icons/Share';
import ChatBubbleIcon from '@material-ui/icons/ChatBubble';

TimeAgo.addDefaultLocale(en);

const useStyles = makeStyles(theme => ({
  root: {
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    flexGrow: 1
  },
}));

class ProductList extends Component {
  constructor(props) {
    super(props);
    this.state = {
      products: [],
    };
  }

  componentDidMount() {
    var self = this;
    axios.get(process.env.REACT_APP_BE_URL + 'all-listings', {
      headers: {
        'Authorization': `Bearer ${this.props.token}` 
      }
    }).then(function(response){
      self.setState({ products: [...response.data.data]});
    });
  }

  handleChatClick(id,e) {
    var self = this;
    var data = {
      'listing_id' : id
    };

    axios.post(process.env.REACT_APP_BE_URL + 'startChat', data, {
      headers: {
        'Authorization': `Bearer ${this.props.token}` 
      }
    }).then(function(response){
      self.props.setActive('conversation', response.data.data.chat_id)
    });
  }

  render() {

    const styles = {
        card: {
          minWidth:100,
          maxWidth: 300,
          minHeight: 600,
          height: 650,
          padding: 2,
          textAlign: 'start',
          media: {
            height: "100%",
            width: "100%"
          },
          position: "relative"
        },
        avatar: {
          backgroundColor: red[500],
        },
        actions: {
          display: "flex",
          bottom: 0,
          position: "absolute",
        },
        chat: {
          display:"flex",
          right: 0,
          bottom:0,
          position: "absolute",
        }
      };
    return (
        <Container>
          <Grid container spacing={1}>
          {this.state.products.map(product => {
            let image = images["p" + product.listing_id];
            return (
              <Grid item xs={12} sm={3} key={product.listing_id}>
                <Card className={String(product.listing_id)} style = {styles.card}>
                  <CardActionArea>
                    <CardHeader
                      avatar={
                        <Avatar aria-label="User" style={styles.avatar}>
                          {product.username.substring(0, 1)}
                        </Avatar>
                      }
                      title={product.username} 
                      subheader={<ReactTimeAgo date={Date.parse(product.listed_date)} locale="en-US" timeStyle="round-minute" />}
                      action={
                        <IconButton aria-label="share">
                          <ShareIcon />
                        </IconButton>
                      }
                    />
                    <CardMedia
                      component = "img"
                      className={String(product.listing_id)}
                      title={product.title}
                      style = {styles.card.media}
                      image = {image}
                    >
                    </CardMedia>
                    <br></br>
                      {product.deprioritized == 1 && <span class ='ml-2 mt-3 p-1 text-light bg-secondary rounded-top rounded-bottom'><small>Deprioritized Listing</small></span>}
                    <CardContent>
                      <TextTruncate line = {1} truncateText="…" text = {product.title} element = "h5" />                            
                      <Typography variant="h5">
                        <b>${product.price}</b><br></br>
                      </Typography>
                      <TextTruncate line = {3} truncateText="…" text = {product.category} element = "p" />
                      <TextTruncate line = {3} truncateText="…" text = {product.description} element = "p" />
                    </CardContent>
                  </CardActionArea>
                  <div className={"flex-grow"} />
                  <CardActions disableSpacing style = {styles.actions}>
                    <IconButton aria-label="add to favorites">
                      <FavoriteIcon />
                    </IconButton>
                  </CardActions>
                  <CardActions disableSpacing style={styles.chat}>
                    <IconButton aria-label="share" onClick={this.handleChatClick.bind(this,product.listing_id)}>
                      <ChatBubbleIcon />
                    </IconButton>
                  </CardActions>
                </Card>
              </Grid>
            );
          })}
          </Grid>
        </Container>
    );
  }
}


export default withStyles(useStyles)(ProductList);



